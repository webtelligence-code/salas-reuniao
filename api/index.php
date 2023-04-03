<?php

header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Headers: *");
header("Access-Control-Allow-Methods: *");

include 'functions.php';

$method = $_SERVER['REQUEST_METHOD'];
$action = isset($_GET['action']) ? $_GET['action'] : '';

switch ($method) {
    case 'GET':
        switch ($action) {
            case 'get_username':
                $username = getUsername();
                echo json_encode($username);
                break;
            case 'get_meetings';
                $meetings = getMeetings();
                echo json_encode($meetings);
                break;
        }
        break;
}
