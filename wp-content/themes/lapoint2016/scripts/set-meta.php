<?php
require_once('set-meta-class.php');

$JSON_MAP = json_decode(file_get_contents(THEME_DIR ."/onpage.json", 0, null, null));

$set_meta = (isset($_GET["action"]) && $_GET["action"] == 1);

$meta = new Set_Meta_Helper($set_meta, $JSON_MAP);

$meta->meta_fix_specific_pages();
$meta->meta_fix_packages();
$meta->meta_fix_destinations();
$meta->meta_fix_levels();
$meta->meta_fix_camps();

exit();
