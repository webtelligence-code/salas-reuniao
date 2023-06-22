<?php

include '../DatabaseConnect.php';

include 'session.php';

$databaseObj = new DatabaseConnect;
$conn = $databaseObj->connect();

// Function that will fetch session username
function getUsername()
{
    return $_SESSION['USERNAME'];
}

/**
 * Function to fetch all meetings from database
 * We need to join the salas table to reunioes table
 * @return void 
 */
function getMeetings()
{
    global $conn;

    $sql = 'SELECT reunioes.id, reunioes.motivo, reunioes.data, reunioes.hora_inicio, reunioes.hora_fim, reunioes.organizador, salas.nome AS sala, salas.url_imagem
            FROM reunioes
            JOIN salas ON reunioes.id_sala = salas.id
            ORDER BY reunioes.data DESC, reunioes.hora_inicio ASC';
    
    $stmt = $conn->prepare($sql);
    $stmt->execute();
    $result = $stmt->get_result();

    $meetings = array();
    while ($row = $result->fetch_assoc()) {
        $meeting = $row;
        
        $participantsSql = 'SELECT participantes.nome_participante
                            FROM participantes
                            WHERE participantes.id_reuniao = ?';
        $stmt = $conn->prepare($participantsSql);
        $stmt->bind_param('i', $meeting['id']);
        $stmt->execute();
        $participantsResult = $stmt->get_result();
        
        $participants = array();
        while ($participantRow = $participantsResult->fetch_assoc()) {
            $participants[] = $participantRow['nome_participante'];
        }
        
        $meeting['participantes'] = $participants;
        
        $meetings[] = $meeting;
    }

    return $meetings;
}


// Function that will fetch all rooms available in the database
function getRooms()
{
    global $conn;
    $sql = 'SELECT * FROM salas';
    $result = mysqli_query($conn, $sql);

    $rooms = array();
    while ($row = mysqli_fetch_assoc($result)) {
        $rooms[] = $row;
    }

    return $rooms;
}


// Function that will fetch all users (guests) from the database
function getUsers()
{
    global $conn;
    $sql = 'SELECT * FROM users WHERE ACT = 1 AND COLABORADOR = 1 ORDER BY NAME ASC';
    $result = mysqli_query($conn, $sql);

    $users = array();
    while ($row = mysqli_fetch_assoc($result)) {
        $users[] = $row;
    }

    return $users;
}

function checkMeetingConflict($id_sala, $data, $hora_inicio, $hora_fim, $id_reuniao)
{
    global $conn;

    $sql = 'SELECT * FROM reunioes
            WHERE id_sala = ?
            AND data = ?
            AND ((? >= hora_inicio AND ? < hora_fim) OR (hora_inicio >= ? AND hora_inicio < ?))';

    if ($id_reuniao) {
        $sql .= ' AND id != ?';
    }

    $stmt = mysqli_prepare($conn, $sql);
    if (!$stmt) {
        throw new Exception('Error in preparing statement: ' . mysqli_error($conn));
    }

    mysqli_stmt_bind_param($stmt, 'isssss', $id_sala, $data, $hora_inicio, $hora_inicio, $hora_fim, $hora_inicio);

    if ($id_reuniao) {
        mysqli_stmt_bind_param($stmt, 'isssssi', $id_sala, $data, $hora_inicio, $hora_inicio, $hora_fim, $hora_inicio, $id_reuniao);
    }

    if (!mysqli_stmt_execute($stmt)) {
        throw new Exception('Error in executing statement: ' . mysqli_error($conn));
    }

    $result = mysqli_stmt_get_result($stmt);
    $meetings = array();

    if (mysqli_num_rows($result) > 0) {
        while ($row = mysqli_fetch_assoc($result)) {
            $meetings[] = $row;
        }
    }

    return count($meetings) > 0;
}

/**
 * Function that will handle insert a new meeting into the database
 * @param mixed $meeting 
 * @return void 
 * @throws PDOException 
 */
function addMeeting($meeting)
{
    global $conn;

    $meetingsSql = 'INSERT INTO reunioes (motivo, data, hora_inicio, hora_fim, organizador, id_sala)
                    VALUES (?, ?, ?, ?, ?, ?)';
    // Add the meeting to the reunioes table
    $stmt = mysqli_prepare($conn, $meetingsSql);
    if (!$stmt) {
        throw new Exception('Error in preparing statement: ' . mysqli_error($conn));
    }

    mysqli_stmt_bind_param($stmt, 'sssssi', $meeting['motivo'], $meeting['data'], $meeting['hora_inicio'], $meeting['hora_fim'], $meeting['organizador'], $meeting['sala']);

    if (!mysqli_stmt_execute($stmt)) {
        throw new Exception('Error in executing statement: ' . mysqli_error($conn));
    }

    // Get the id of the inserted meeting
    $meeting_id = mysqli_insert_id($conn);

    $guestsSql = 'INSERT INTO participantes (id_reuniao, nome_participante)
                    VALUES (?, ?)';
    // Add the guests to the participantes table
    foreach ($meeting['participantes'] as $participante) {
        $stmt = mysqli_prepare($conn, $guestsSql);
        if (!$stmt) {
            throw new Exception('Error in preparing statement: ' . mysqli_error($conn));
        }

        mysqli_stmt_bind_param($stmt, 'is', $meeting_id, $participante);

        if (!mysqli_stmt_execute($stmt)) {
            throw new Exception('Error in executing statement: ' . mysqli_error($conn));
        }
    }

    return [
        'status' => 'success',
        'message' => 'Reunião adicionada com sucesso!',
        'title' => 'Sucesso!'
    ];
}

/**
 * This function will update the meeting in the database
 * @param object $meeting 
 * @return string[] response array
 * @throws PDOException 
 */
function updateMeeting($meeting)
{
    global $conn;

    $meetingSql = 'UPDATE reunioes 
                    SET motivo = ?, data = ?, hora_inicio = ?, hora_fim = ?, organizador = ?, id_sala = ? 
                    WHERE id = ?';
    // Update the meeting in the reunioes table
    $stmt = mysqli_prepare($conn, $meetingSql);
    if (!$stmt) {
        throw new Exception('Error in preparing statement: ' . mysqli_error($conn));
    }

    mysqli_stmt_bind_param($stmt, 'ssssssi', $meeting['motivo'], $meeting['data'], $meeting['hora_inicio'], $meeting['hora_fim'], $meeting['organizador'], $meeting['sala'], $meeting['meeting_id']);

    if (!mysqli_stmt_execute($stmt)) {
        throw new Exception('Error in executing statement: ' . mysqli_error($conn));
    }

    $deleteGuestsSql = 'DELETE FROM participantes WHERE id_reuniao = ?';
    // Delete the existing guests in the guests table
    $stmt = mysqli_prepare($conn, $deleteGuestsSql);
    if (!$stmt) {
        throw new Exception('Error in preparing statement: ' . mysqli_error($conn));
    }

    mysqli_stmt_bind_param($stmt, 'i', $meeting['meeting_id']);

    if (!mysqli_stmt_execute($stmt)) {
        throw new Exception('Error in executing statement: ' . mysqli_error($conn));
    }

    $updateGuestsSql = 'INSERT INTO participantes (id_reuniao, nome_participante) 
                        VALUES (?, ?)';
    // Add the updated guests to the guests table
    foreach ($meeting['participantes'] as $participante) {
        $stmt = mysqli_prepare($conn, $updateGuestsSql);
        if (!$stmt) {
            throw new Exception('Error in preparing statement: ' . mysqli_error($conn));
        }

        mysqli_stmt_bind_param($stmt, 'is', $meeting['meeting_id'], $participante);

        if (!mysqli_stmt_execute($stmt)) {
            throw new Exception('Error in executing statement: ' . mysqli_error($conn));
        }
    }

    return [
        'status' => 'success',
        'message' => 'Reunião atualizada com sucesso!',
        'title' => 'Atualizada!'
    ];
}


/**
 * This function will handle a transaction to delete meeting in reunioes table
 * and also delete all the participantes associated to the meeting in question
 * @param object $meeting_id 
 * @return bool|void 
 * @throws PDOException 
 */
function deleteMeeting($meeting_id)
{
    global $conn;

    try {
        // Start a transaction
        mysqli_begin_transaction($conn);

        // Delete the associated guests
        $guestsSql = 'DELETE FROM participantes WHERE id_reuniao = ?';
        $stmt = mysqli_prepare($conn, $guestsSql);
        if (!$stmt) {
            throw new Exception('Error in preparing statement: ' . mysqli_error($conn));
        }
        mysqli_stmt_bind_param($stmt, 'i', $meeting_id);
        if (!mysqli_stmt_execute($stmt)) {
            throw new Exception('Error in executing statement: ' . mysqli_error($conn));
        }

        // Delete the meeting
        $meetingsSql = 'DELETE FROM reunioes WHERE ID = ?';
        $stmt = mysqli_prepare($conn, $meetingsSql);
        if (!$stmt) {
            throw new Exception('Error in preparing statement: ' . mysqli_error($conn));
        }
        mysqli_stmt_bind_param($stmt, 'i', $meeting_id);
        $result = mysqli_stmt_execute($stmt);

        // Commit the transaction
        mysqli_commit($conn);

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

        return $response;
    } catch (Exception $e) {
        // Rollback the transaction if there is an error
        mysqli_rollback($conn);
        error_log('Error while deleting meeting: ' . $e->getMessage());
    }
}
