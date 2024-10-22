<?php
@session_start();
$is_session =  session_destroy();
if ($is_session) {
    echo json_encode(['result' => true, 'status' => 'success']);
} else {
    echo json_encode(['result' => false, 'status' => 'error']);
    http_response_code(400);
}
