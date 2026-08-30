<?php
/**
 * ---------------------------------------------------------------------------
 *  partials/nav.php — sidebar navigation, filtered by role
 * ---------------------------------------------------------------------------
 *  Each item:
 *    key   -> matches $active_nav set by the page
 *    label -> visible text
 *    icon  -> Bootstrap Icons class
 *    file  -> page file name in frontend/pages/
 *    roles -> '*' (everyone signed in) OR an array of allowed role slugs
 *             ('admin' always passes)
 *
 *  admin_top.php calls nav_for_role(current_role()) to get the visible menu.
 *  Guarded so the file is safe to include more than once.
 * ---------------------------------------------------------------------------
 */

if (!function_exists('nav_all')) {

    /** The full menu definition. */
    function nav_all(): array
    {
        return [
            'Overview' => [
                ['key' => 'dashboard', 'label' => 'Dashboard', 'icon' => 'bi-speedometer2', 'file' => 'AdminDashboard.php',    'roles' => ['admin', 'official', 'sk_chairman', 'treasurer']],
                ['key' => 'dashboard', 'label' => 'Dashboard', 'icon' => 'bi-speedometer2', 'file' => 'ResidentDashboard.php', 'roles' => ['resident']],
            ],
            'My Portal' => [
                ['key' => 'myrequests',  'label' => 'My Document Requests', 'icon' => 'bi-file-earmark-text', 'file' => 'MyRequests.php',  'roles' => ['resident']],
                ['key' => 'request_new', 'label' => 'Request a Document',   'icon' => 'bi-plus-square',       'file' => 'Certificate.php', 'roles' => ['resident']],
                ['key' => 'mymessages',  'label' => 'My Messages',          'icon' => 'bi-chat-left-dots',    'file' => 'MyMessages.php',  'roles' => ['resident']],
            ],
            'Work' => [
                ['key' => 'tasks',    'label' => 'Tasks & Activities',   'icon' => 'bi-check2-square',  'file' => 'Tasks.php',    'roles' => ['admin', 'official', 'sk_chairman', 'treasurer']],
                ['key' => 'activity', 'label' => 'Barangay Activities',  'icon' => 'bi-calendar-event', 'file' => 'Activity.php', 'roles' => ['admin', 'official', 'sk_chairman', 'treasurer']],
            ],
            'Records' => [
                ['key' => 'officials', 'label' => 'Barangay Officials', 'icon' => 'bi-people',       'file' => 'BarangayOfficial.php', 'roles' => ['admin', 'official', 'sk_chairman', 'treasurer']],
                ['key' => 'residents', 'label' => 'Residents',          'icon' => 'bi-person-vcard', 'file' => 'Resident.php',         'roles' => ['admin', 'official', 'sk_chairman']],
                ['key' => 'blotter',   'label' => 'Blotter',            'icon' => 'bi-journal-text', 'file' => 'Blotter.php',          'roles' => ['admin', 'official']],
            ],
            'Services' => [
                ['key' => 'requests', 'label' => 'Document Requests', 'icon' => 'bi-file-earmark-text', 'file' => 'DocumentRequest.php',         'roles' => ['admin', 'official', 'treasurer']],
                ['key' => 'forms',    'label' => 'Certificates',      'icon' => 'bi-award',             'file' => 'Forms.php',                   'roles' => ['admin', 'treasurer']],
                ['key' => 'messages', 'label' => 'Messages',          'icon' => 'bi-inbox',             'file' => 'BarangayContact&Message.php', 'roles' => ['admin', 'official', 'sk_chairman', 'treasurer']],
            ],
            'Content' => [
                ['key' => 'information', 'label' => 'Barangay Information', 'icon' => 'bi-info-circle',     'file' => 'Information.php',  'roles' => ['admin', 'official']],
                ['key' => 'faq',        'label' => 'FAQ',                  'icon' => 'bi-question-circle', 'file' => 'BarangayFAQ.php', 'roles' => ['admin', 'official']],
            ],
            'Administration' => [
                ['key' => 'users', 'label' => 'System Users', 'icon' => 'bi-shield-lock', 'file' => 'Users.php', 'roles' => ['admin']],
            ],
            'Account' => [
                ['key' => 'profile', 'label' => 'My Profile', 'icon' => 'bi-person-circle', 'file' => 'UserProfile.php', 'roles' => '*'],
            ],
        ];
    }

    /** Menu with only the sections/items the given role may see. */
    function nav_for_role(string $role): array
    {
        $out = [];
        foreach (nav_all() as $section => $items) {
            $visible = array_values(array_filter($items, static function ($item) use ($role) {
                $r = $item['roles'];
                // '*' = everyone; otherwise the role must be listed explicitly.
                // (admin is listed on every non-resident item, so admin does
                //  NOT see the resident-only "My Portal" entries.)
                return $r === '*' || in_array($role, (array) $r, true);
            }));
            if ($visible) {
                $out[$section] = $visible;
            }
        }
        return $out;
    }
}
