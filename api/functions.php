<?php

include 'DatabaseConnect.php';

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
    $sql = 'SELECT reunioes.id, reunioes.motivo, reunioes.data, reunioes.hora_inicio, reunioes.hora_fim, reunioes.organizador, salas.nome 
            AS sala, salas.url_imagem
            FROM reunioes
            JOIN salas ON reunioes.id_sala = salas.id
            ORDER BY reunioes.data DESC, reunioes.hora_inicio ASC
    ';
    $stmt = $conn->prepare($sql);
    $stmt->execute();
    $meetings = $stmt->fetchAll(PDO::FETCH_ASSOC);

    return $meetings;
}

// Function that will fetch all rooms available in the database
function getRooms()
{
    global $conn;
    $sql = 'SELECT * FROM salas';
    $stmt = $conn->prepare($sql);
    $stmt->execute();
    $rooms = $stmt->fetchAll(PDO::FETCH_ASSOC);

    return $rooms;
}

// Function that will fetch all users (guests) from the database
function getUsers()
{
    global $conn;
    $sql = 'SELECT * FROM users ORDER BY NAME ASC';
    $stmt = $conn->prepare($sql);
    $stmt->execute();
    $users = $stmt->fetchAll(PDO::FETCH_ASSOC);

    return $users;
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
            VALUES (:motivo, :data, :hora_inicio, :hora_fim, :organizador, :sala)';
    // Add the meeting to the reunioes table
    $stmt = $conn->prepare($meetingsSql);
    $stmt->bindParam(':motivo', $meeting['motivo']);
    $stmt->bindParam(':data', $meeting['data']);
    $stmt->bindParam(':hora_inicio', $meeting['hora_inicio']);
    $stmt->bindParam(':hora_fim', $meeting['hora_fim']);
    $stmt->bindParam(':organizador', $meeting['organizador']);
    $stmt->bindParam(':sala', $meeting['sala']);
    $stmt->execute();

    // Get the id of the inserted meeting
    $meeting_id = $conn->lastInsertId();

    $guestsSql = 'INSERT INTO participantes (id_reuniao, nome_participante)
                    VALUES (:id_reuniao, :nome_participante)';
    // Add the guests to the participantes table
    foreach ($meeting['participantes'] as $participante) {
        $stmt = $conn->prepare($guestsSql);
        $stmt->bindParam(':id_reuniao', $meeting_id);
        $stmt->bindParam(':nome_participante', $participante);

        $stmt->execute();
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
                    SET motivo = :motivo, data = :data, hora_inicio = :hora_inicio, hora_fim = :hora_fim, organizador = :organizador, id_sala = :sala 
                    WHERE id = :id';
    // Update the meeting in the reunioes table
    $stmt = $conn->prepare($meetingSql);
    $stmt->bindParam(':motivo', $meeting['motivo']);
    $stmt->bindParam(':data', $meeting['data']);
    $stmt->bindParam(':hora_inicio', $meeting['hora_inicio']);
    $stmt->bindParam(':hora_fim', $meeting['hora_fim']);
    $stmt->bindParam(':organizador', $meeting['organizador']);
    $stmt->bindParam(':sala', $meeting['sala']);
    $stmt->bindParam(':id', $meeting['meeting_id']);
    $stmt->execute();

    $deleteGuestsSql = 'DELETE FROM participantes WHERE id_reuniao = :id_reuniao';
    // Delete the existing guests in the guests table
    $stmt = $conn->prepare($deleteGuestsSql);
    $stmt->bindParam(':id_reuniao', $meeting['meeting_id']);
    $stmt->execute();

    $updateGuestsSql = 'INSERT INTO participantes (id_reuniao, nome_participante) 
                        VALUES (:id_reuniao, :nome_participante)';
    // Add the updated guests to the guests table
    foreach ($meeting['participantes'] as $participante) {
        $stmt = $conn->prepare($updateGuestsSql);
        $stmt->bindParam(':id_reuniao', $meeting['meeting_id']);
        $stmt->bindParam(':nome_participante', $participante);
        $stmt->execute();
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
        $conn->beginTransaction();

        // Delete the associated guests
        $guestsSql = 'DELETE FROM participantes WHERE id_reuniao = :meeting_id';
        $stmt = $conn->prepare($guestsSql);
        $stmt->bindParam(':meeting_id', $meeting_id, PDO::PARAM_INT);
        $stmt->execute();

        // Delete the meeting
        $meetingsSql = 'DELETE FROM reunioes WHERE ID = :meeting_id';
        $stmt = $conn->prepare($meetingsSql);
        $stmt->bindParam(':meeting_id', $meeting_id, PDO::PARAM_INT);
        $result = $stmt->execute();

        // Commit the transaction
        $conn->commit();

        return $result;
    } catch (PDOException $e) {
        // Rollback the transaction if there is an error
        $conn->rollBack();
        error_log('Error while deleting meeting: ' . $e->getMessage());
    }
}