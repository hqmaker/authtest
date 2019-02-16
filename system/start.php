<?php
error_reporting(-1);
ini_set ('display_errors', true);
$startTime = microtime(true);

require_once 'classes/Db.php';
require_once 'classes/Auth.php';
require_once 'functions.php';

const DB_HOST = 'mysql2.justhost.ru';
const DB_USER = 'u689512a7_test';
const DB_PASS = 'testtest';
const DB_NAME = 'u689512a7_test';
const DB_PORT = 3306;

const SHOW_QUERIES = true;
const DB_DEBUG = true;

$db = new Db();

const DOMAIN = 'DOMAIN';
const LANG = 'ru';

session_start();
