<?php
/**
 * ---------------------------------------------------------------------------
 *  api/resources.php — the REST resource registry
 * ---------------------------------------------------------------------------
 *  Each entry drives the generic CRUD controller (api/Resource.php):
 *
 *    table        real table name
 *    pk           primary-key column
 *    fillable     columns a client may write (anything else is ignored)
 *    required     columns that must be present on create / PUT
 *    searchable   columns matched by ?search=
 *    hidden       columns never returned in a response
 *    transform    [col => 'hash']  server-side value transforms on write
 *    order        default ORDER BY for the list endpoint
 *    methods      allowed HTTP verbs (default: all five)
 *    read_roles   roles allowed to GET      ([] = admin only)
 *    write_roles  roles allowed to write    ([] = admin only)
 * ---------------------------------------------------------------------------
 */

declare(strict_types=1);

/** @return array<string,array<string,mixed>> resource slug => config */
function api_resources(): array
{
    return [

        'residents' => [
            'table'      => 'residents',
            'pk'         => 'id',
            'fillable'   => ['photo', 'full_name', 'birth_date', 'birth_place', 'age', 'total_households',
                             'contact', 'blood_type', 'civil_status', 'occupation', 'monthly_income',
                             'household', 'length_of_stay', 'religion', 'nationality', 'gender', 'education'],
            'required'   => ['full_name'],
            'searchable' => ['full_name', 'contact', 'occupation', 'religion'],
            'order'      => 'id DESC',
        ],

        'officials' => [
            'table'      => 'barangay_officials',
            'pk'         => 'id',
            'fillable'   => ['position', 'photo', 'fullName', 'contact', 'address', 'startOfTerm', 'endOfTerm'],
            'required'   => ['fullName', 'position'],
            'searchable' => ['fullName', 'position', 'contact'],
            'order'      => 'id ASC',
        ],

        'blotter' => [
            'table'      => 'blotterrecords',
            'pk'         => 'id',
            'fillable'   => ['status', 'complainant', 'age1', 'address1', 'contact1', 'personToComplaint',
                             'age2', 'address2', 'contact2', 'actionTaken'],
            'required'   => ['complainant', 'personToComplaint'],
            'searchable' => ['complainant', 'personToComplaint', 'status'],
            'order'      => 'id DESC',
        ],

        'certificates' => [
            'table'      => 'certificates',
            'pk'         => 'id',
            'fillable'   => ['certificate_name', 'requirements', 'file'],
            'required'   => ['certificate_name'],
            'searchable' => ['certificate_name'],
            'order'      => 'certificate_name ASC',
        ],

        'document-requests' => [
            'table'       => 'document_requests',
            'pk'          => 'id',
            'fillable'    => ['certificate_id', 'fullName', 'age', 'purpose', 'address', 'dob',
                              'civilStatus', 'placeOfBirth', 'sex', 'email', 'business'],
            'required'    => ['fullName', 'certificate_id'],
            'searchable'  => ['fullName', 'email', 'purpose'],
            'order'       => 'request_date DESC, id DESC',
            'read_roles'  => ['treasurer'],
            'write_roles' => ['treasurer'],
        ],

        'tasks' => [
            'table'       => 'tasks',
            'pk'          => 'id',
            'fillable'    => ['title', 'description', 'assignee_email', 'assignee_name', 'assignee_role',
                              'status', 'priority', 'due_date', 'created_by', 'attachment', 'note'],
            'required'    => ['title'],
            'searchable'  => ['title', 'assignee_name', 'status'],
            'order'       => 'id DESC',
            'read_roles'  => ['official', 'sk_chairman', 'treasurer'],
            'write_roles' => [],   // admin only
        ],

        'faqs' => [
            'table'      => 'faq',
            'pk'         => 'id',
            'fillable'   => ['question', 'answer', 'date'],
            'required'   => ['question', 'answer'],
            'searchable' => ['question', 'answer'],
            'order'      => 'id DESC',
        ],

        'activities' => [
            'table'      => 'activity',
            'pk'         => 'id',
            'fillable'   => ['photos', 'date', 'activity', 'description'],
            'required'   => ['activity'],
            'searchable' => ['activity', 'description'],
            'order'      => 'date DESC, id DESC',
        ],

        'contacts' => [
            'table'      => 'contacts',
            'pk'         => 'id',
            'fillable'   => ['label', 'description', 'contacts'],
            'required'   => ['label'],
            'searchable' => ['label', 'description'],
            'order'      => 'id ASC',
        ],

        'messages' => [
            'table'      => 'receivemessages',
            'pk'         => 'id',
            'fillable'   => [],
            'required'   => [],
            'searchable' => ['name', 'email', 'message'],
            'order'      => 'created_at DESC, id DESC',
            'methods'    => ['GET', 'DELETE'],   // written by the public contact form only
        ],

        'users' => [
            'table'      => 'users',
            'pk'         => 'id',
            'fillable'   => ['fullName', 'userName', 'password', 'userType'],
            'required'   => ['fullName', 'userName', 'password', 'userType'],
            'searchable' => ['fullName', 'userName'],
            'hidden'     => ['password'],
            'transform'  => ['password' => 'hash'],
            'order'      => 'id ASC',
        ],
    ];
}
