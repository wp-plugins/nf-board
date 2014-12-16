<?php
$uploadPath = wp_upload_dir();
define("NFB_VER", "v1.0.2");
define("NFB_ABS", plugin_dir_path(__FILE__));
define("NFB_WEB", plugins_url('/',__FILE__));
define("NFB_HOME_URL", home_url());
define("NFB_SITE_URL", site_url());
define("NFB_CONTENT_URL", content_url());
define("NFB_UPLOAD_PATH", $uploadPath['basedir']."/");
define("NFB_SETUP", admin_url("/admin.php?page=NFBoard"));
define("NFB_BOARD_LIST", admin_url("/admin.php?page=NFBoardList"));
define("NFB_BOARD_ADD", admin_url("/admin.php?page=NFBoardAdd"));
define("NFB_MEMBER_LIST", admin_url("/admin.php?page=NFMemberList"));
?>