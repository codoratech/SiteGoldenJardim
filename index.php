<?php
require_once __DIR__ . '/includes/site_content.php';
require_once __DIR__ . '/includes/site_sections.php';
$site_content = site_content_load();
require __DIR__ . '/templates/public-page.php';
