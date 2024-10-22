<?php
function connect_db()
{
    $host = 'localhost';
    $db = 'librarydb';
    $username = 'root';
    $password = '';
    return  new PDO("mysql:host=$host;dbname=$db", $username, $password);
}
