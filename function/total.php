<?php
require_once('./config/config_db.php');
function get_total($sql, $params)
{
    try {
        $stmt = connect_db()->prepare($sql);
        $stmt->execute($params);
        $row =  $stmt->fetch(PDO::FETCH_ASSOC);
        return (float)$row['count'];
    } catch (PDOException $e) {
        return 0;
    }
}
