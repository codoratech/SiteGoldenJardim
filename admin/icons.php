<?php
function admin_icon($name, $class = '') {
    $paths = [
        'globe' => '<circle cx="12" cy="12" r="9"/><path d="M3 12h18M12 3c5 5 5 13 0 18-5-5-5-13 0-18Z"/>',
        'image' => '<rect x="3" y="3" width="18" height="18" rx="2"/><circle cx="8" cy="8" r="1.5"/><path d="m21 15-5-5L5 21"/>',
        'up' => '<path d="M12 19V5m-6 6 6-6 6 6"/>',
        'down' => '<path d="M12 5v14m-6-6 6 6 6-6"/>',
        'edit' => '<path d="m15 4 5 5-11 11-6 1 1-6L15 4Zm-2 2 5 5"/>',
        'trash' => '<path d="M3 6h18M9 6V3h6v3M5 6l1 15h12l1-15M10 10v7m4-7v7"/>',
        'lock' => '<rect x="5" y="10" width="14" height="11" rx="2"/><path d="M8 10V6a4 4 0 0 1 8 0v4m-4 4v3"/>',
        'check' => '<path d="m5 12 4 4L19 6"/>',
        'leaf' => '<path d="M20 4c0 9-3 15-10 15a6 6 0 0 1-6-6C4 6 11 4 20 4Z"/><path d="m4 21 11-11"/>',
        'grid' => '<rect x="3" y="3" width="7" height="7" rx="2"/><rect x="14" y="3" width="7" height="7" rx="2"/><rect x="3" y="14" width="7" height="7" rx="2"/><rect x="14" y="14" width="7" height="7" rx="2"/>',
        'users' => '<path d="M16 21v-2a4 4 0 0 0-4-4H6a4 4 0 0 0-4 4v2m20 0v-2a4 4 0 0 0-3-3.87M16 3a4 4 0 0 1 0 8"/><circle cx="9" cy="7" r="4"/>',
        'box' => '<path d="m21 8-9-5-9 5v9l9 5 9-5V8Zm-18 0 9 5 9-5M12 13v9M7.5 5.5l9 5"/>',
        'clipboard' => '<rect x="5" y="5" width="14" height="16" rx="2"/><rect x="9" y="2" width="6" height="6" rx="1"/><path d="M9 13h6m-6 4h6"/>',
        'calendar' => '<rect x="3" y="5" width="18" height="16" rx="2"/><path d="M16 3v4M8 3v4M3 11h18m-12 5h2"/>',
        'wallet' => '<path d="M21 8H5a2 2 0 0 1 0-4h14v4M3 6v13a2 2 0 0 0 2 2h16V8m0 4h-5v5h5"/><path d="M17 14.5h.01"/>',
        'truck' => '<path d="M1 3h14v14H1V3Zm14 5h4l3 4v5h-7"/><circle cx="5.5" cy="19" r="2"/><circle cx="18.5" cy="19" r="2"/>',
        'key' => '<circle cx="8" cy="8" r="5"/><path d="m12 12 9 9m-3-3 3-3m-6 0 3-3"/>',
        'plus' => '<path d="M12 5v14M5 12h14"/>',
        'arrow' => '<path d="M5 12h14m-6-6 6 6-6 6"/>',
        'chevron' => '<path d="m6 9 6 6 6-6"/>',
        'menu' => '<path d="M4 6h16M4 12h16M4 18h16"/>',
        'close' => '<path d="m6 6 12 12M6 18 18 6"/>',
        'bell' => '<path d="M18 8a6 6 0 0 0-12 0c0 7-3 7-3 9h18c0-2-3-2-3-9M10 21h4"/>',
        'search' => '<circle cx="10.5" cy="10.5" r="7.5"/><path d="m16 16 5 5"/>',
        'sun' => '<circle cx="12" cy="12" r="4"/><path d="M12 2v2m0 16v2M2 12h2m16 0h2M5 5l1.5 1.5m11 11L19 19M5 19l1.5-1.5m11-11L19 5"/>',
        'moon' => '<path d="M20.9 13A9 9 0 0 1 11 3.1 9 9 0 1 0 20.9 13Z"/>',
        'logout' => '<path d="M9 21H4V3h5m7 4 5 5-5 5M8 12h13"/>',
        'alert' => '<path d="m12 3 10 18H2L12 3Zm0 6v4m0 4h.01"/>',
    ];
    return '<svg class="admin-icon ' . htmlspecialchars($class, ENT_QUOTES, 'UTF-8') . '" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.7" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">' . ($paths[$name] ?? $paths['grid']) . '</svg>';
}
