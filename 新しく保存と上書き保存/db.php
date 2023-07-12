<?php
function db()
{
    try {
        $db_file = "sample.sqlite3";
        $db = new PDO("sqlite:$db_file");
        

        return $db;
    } catch (PDOException $e) {
        print($e->getMessage());
    }
}
