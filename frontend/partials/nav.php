<?php
/**
 * ---------------------------------------------------------------------------
 *  partials/nav.php — the admin sidebar navigation definition
 * ---------------------------------------------------------------------------
 *  One place to edit the menu. Grouped into sections. Each item:
 *    key   -> matches $active_nav set by the page
 *    label -> visible text
 *    icon  -> Bootstrap Icons class
 *    file  -> page file name in frontend/pages/
 * ---------------------------------------------------------------------------
 */

return [
    'Overview' => [
        ['key' => 'dashboard', 'label' => 'Dashboard',        'icon' => 'bi-speedometer2', 'file' => 'AdminDashboard.php'],
    ],
    'Records' => [
        ['key' => 'officials',  'label' => 'Barangay Officials', 'icon' => 'bi-people',        'file' => 'BarangayOfficial.php'],
        ['key' => 'residents',  'label' => 'Residents',          'icon' => 'bi-person-vcard',  'file' => 'Resident.php'],
        ['key' => 'blotter',    'label' => 'Blotter',            'icon' => 'bi-journal-text',  'file' => 'Blotter.php'],
        ['key' => 'activity',   'label' => 'Activities',         'icon' => 'bi-calendar-event','file' => 'Activity.php'],
    ],
    'Services' => [
        ['key' => 'requests',   'label' => 'Document Requests',  'icon' => 'bi-file-earmark-text', 'file' => 'DocumentRequest.php'],
        ['key' => 'forms',      'label' => 'Certificates',       'icon' => 'bi-award',         'file' => 'Forms.php'],
        ['key' => 'messages',   'label' => 'Messages',           'icon' => 'bi-chat-left-dots','file' => 'BarangayContact&Message.php'],
    ],
    'Content' => [
        ['key' => 'information', 'label' => 'Barangay Information','icon' => 'bi-info-circle',  'file' => 'Information.php'],
        ['key' => 'faq',        'label' => 'FAQ',                'icon' => 'bi-question-circle','file' => 'BarangayFAQ.php'],
    ],
    'Administration' => [
        ['key' => 'users',      'label' => 'System Users',       'icon' => 'bi-shield-lock',   'file' => 'Users.php'],
    ],
];
