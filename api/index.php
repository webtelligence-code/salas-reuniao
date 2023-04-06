<?php

header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Headers: *");
header("Access-Control-Allow-Methods: *");

include 'functions.php';

$method = $_SERVER['REQUEST_METHOD'];

switch ($method) {
    case 'GET':
        $get_action = isset($_GET['action']) ? $_GET['action'] : '';
        switch ($get_action) {
            case 'get_username':
                $username = getUsername();
                echo json_encode($username);
                break;
            case 'get_meetings';
                $meetings = getMeetings();
                echo json_encode($meetings);
                break;
            case 'get_rooms':
                $rooms = getRooms();
                echo json_encode($rooms);
                break;
            case 'get_users':
                $users = getUsers();
                echo json_encode($users);
                break;
        }
        break;
        // Case for delete meeting by id
    case 'DELETE':
        parse_str(file_get_contents("php://input"), $_DELETE);
        $delete_action = isset($_DELETE['action']) ? $_DELETE['action'] : '';

        switch ($delete_action) {
            case 'delete_meeting':
                $meeting_id = isset($_DELETE['meeting_id']) ? $_DELETE['meeting_id'] : '';
                if ($meeting_id) {
                    $result = deleteMeeting($meeting_id);
                    if ($result) {
                        $response = [
                            'status' => 'success', 
                            'message' => 'Reunião removida com sucesso!',
                            'title' => 'Removida!'
                        ];
                    } else {
                        $response = [
                            'status' => 'error', 
                            'message' => 'Erro ao remover reunião da base de dados.',
                            'title' => 'Erro ao remover.'
                        ];
                    }
                } else {
                    $response = [
                        'status' => 'error', 
                        'message' => 'É necessário o id da reunião.',
                        'title' => 'ID em falta.'
                    ];
                }
                echo json_encode($response);
                break;
        }
        break;
}