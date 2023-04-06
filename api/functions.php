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
            JOIN salas ON reunioes.id_sala = salas.id;
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
function getUsers() {
    global $conn;
    $sql = 'SELECT * FROM users ORDER BY NAME ASC';
    $stmt = $conn->prepare($sql);
    $stmt->execute();
    $users = $stmt->fetchAll(PDO::FETCH_ASSOC);

    return $users;
}

/**
 * This function will handle a transaction to delete meeting in reunioes table
 * and also delete all the participantes associated to the meeting in question
 * @param object $meeting_id 
 * @return bool|void 
 * @throws PDOException 
 */
function deleteMeeting($meeting_id) {
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