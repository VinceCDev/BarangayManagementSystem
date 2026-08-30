<?php
/**
 * ---------------------------------------------------------------------------
 *  api/Resource.php — generic CRUD controller over a single PDO table
 * ---------------------------------------------------------------------------
 *  Driven entirely by one entry from api/resources.php. Column names come
 *  from that whitelist (never from the request), and every value is bound
 *  through a prepared statement, so it is safe against SQL injection.
 * ---------------------------------------------------------------------------
 */

declare(strict_types=1);

require_once __DIR__ . '/Http.php';
require_once __DIR__ . '/../config/database.php';

final class Resource
{
    private PDO $db;

    public function __construct(private array $cfg)
    {
        $this->db = db();
        $this->cfg += [
            'pk' => 'id', 'fillable' => [], 'required' => [], 'searchable' => [],
            'hidden' => [], 'transform' => [], 'order' => 'id DESC',
            'methods' => ['GET', 'POST', 'PUT', 'PATCH', 'DELETE'],
        ];
    }

    public function allows(string $method): bool
    {
        return in_array(strtoupper($method), $this->cfg['methods'], true);
    }

    /* ---- GET /{resource} -------------------------------------------------- */
    public function index(): never
    {
        [$page, $per, $offset] = api_pagination();
        $table = $this->cfg['table'];

        $where = '';
        $args  = [];
        $search = trim((string) ($_GET['search'] ?? ''));
        if ($search !== '' && $this->cfg['searchable']) {
            $parts = [];
            foreach ($this->cfg['searchable'] as $col) {
                $parts[] = "`$col` LIKE ?";
                $args[]  = '%' . $search . '%';
            }
            $where = 'WHERE ' . implode(' OR ', $parts);
        }

        $total = (int) $this->run("SELECT COUNT(*) FROM `$table` $where", $args)->fetchColumn();

        $rows = $this->run(
            "SELECT * FROM `$table` $where ORDER BY {$this->cfg['order']} LIMIT $per OFFSET $offset",
            $args
        )->fetchAll();

        api_ok(
            array_map([$this, 'clean'], $rows),
            ['page' => $page, 'per_page' => $per, 'total' => $total, 'total_pages' => (int) ceil($total / $per) ?: 1]
        );
    }

    /* ---- GET /{resource}/{id} ------------------------------------------- */
    public function show(string $id): never
    {
        api_ok($this->clean($this->find($id)));
    }

    /* ---- POST /{resource} --------------------------------------------- */
    public function store(array $body): never
    {
        $data = $this->collect($body, requireAll: true);

        $cols = array_keys($data);
        $ph   = implode(', ', array_fill(0, count($cols), '?'));
        $sql  = "INSERT INTO `{$this->cfg['table']}` (`" . implode('`, `', $cols) . "`) VALUES ($ph)";
        $this->run($sql, array_values($data));

        $id = (string) $this->db->lastInsertId();
        api_ok($this->clean($this->find($id)), [], 201);
    }

    /* ---- PUT / PATCH /{resource}/{id} -------------------------------- */
    public function update(string $id, array $body, bool $partial): never
    {
        $this->find($id);   // 404 if it doesn't exist
        $data = $this->collect($body, requireAll: !$partial);

        if (!$data) {
            api_fail('No writable fields were supplied.', 422, 'nothing_to_update');
        }

        $set = implode(', ', array_map(static fn ($c) => "`$c` = ?", array_keys($data)));
        $this->run(
            "UPDATE `{$this->cfg['table']}` SET $set WHERE `{$this->cfg['pk']}` = ?",
            [...array_values($data), $id]
        );

        api_ok($this->clean($this->find($id)));
    }

    /* ---- DELETE /{resource}/{id} ----------------------------------- */
    public function destroy(string $id): never
    {
        $this->find($id);
        $this->run("DELETE FROM `{$this->cfg['table']}` WHERE `{$this->cfg['pk']}` = ?", [$id]);
        api_send(null, 204);
    }

    /* =================================================================== */

    private function find(string $id): array
    {
        $row = $this->run(
            "SELECT * FROM `{$this->cfg['table']}` WHERE `{$this->cfg['pk']}` = ? LIMIT 1",
            [$id]
        )->fetch();

        if (!$row) {
            api_fail('Resource not found.', 404, 'not_found');
        }
        return $row;
    }

    /** Keep only fillable keys, enforce required, apply transforms. */
    private function collect(array $body, bool $requireAll): array
    {
        $out     = [];
        $missing = [];

        foreach ($this->cfg['fillable'] as $col) {
            if (array_key_exists($col, $body)) {
                $val = $body[$col];
                if (($this->cfg['transform'][$col] ?? '') === 'hash') {
                    $val = password_hash((string) $val, PASSWORD_DEFAULT);
                }
                $out[$col] = is_scalar($val) || $val === null ? $val : json_encode($val);
            } elseif ($requireAll && in_array($col, $this->cfg['required'], true)) {
                $missing[$col] = 'This field is required.';
            }
        }

        if ($requireAll) {
            foreach ($this->cfg['required'] as $col) {
                if (($out[$col] ?? '') === '' && !isset($missing[$col])) {
                    $missing[$col] = 'This field is required.';
                }
            }
        }
        if ($missing) {
            api_fail('Validation failed.', 422, 'validation', $missing);
        }
        return $out;
    }

    /** Drop hidden columns before returning a row. */
    private function clean(array $row): array
    {
        foreach ($this->cfg['hidden'] as $col) {
            unset($row[$col]);
        }
        return $row;
    }

    private function run(string $sql, array $args = []): PDOStatement
    {
        try {
            $st = $this->db->prepare($sql);
            $st->execute($args);
            return $st;
        } catch (Throwable $e) {
            error_log('API Resource: ' . $e->getMessage() . ' [' . $sql . ']');
            api_fail('The request could not be processed.', 500, 'server_error');
        }
    }
}
