<?php
ini_set("display_errors", true);
date_default_timezone_set("Asia/Tokyo");
define("DB_DSN", "mysql:host=localhost;port=3307;dbname=cms");
define("DB_USERNAME", "usr");
define("DB_PASSWORD", "password");
define("CLASS_PATH", "classes");
define("TEMPLATE_PATH", "templates");
define("HOMEPAGE_NUM_ARTICLES", 5);
define("ADMIN_USERNAME", "admin");
define("ADMIN_PASSWORD", "mypass");

function getPDO() {
    static $pdo;
    if (!isset($pdo)) {
        try {
            $pdo = new PDO(DB_DSN, DB_USERNAME, DB_PASSWORD);
        } catch (Exception $e) {
            print('Connection failed:' . $e->getMessage());
            die();
        }
    }
    return $pdo;
}

require_once(CLASS_PATH."/article.php");

function handleException($exception) {
    echo "Sorry, a problem occurred. Please try later.";
    error_log($exception->getMessage());
}
set_exception_handler('handleException');
?>