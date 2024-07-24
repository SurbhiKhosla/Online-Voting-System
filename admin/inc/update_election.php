<?php
include_once 'config.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $id = intval($_POST['id']);
    $election_topic = mysqli_real_escape_string($db, $_POST['election_topic']);
    $number_of_candidates = intval($_POST['number_of_candidates']);
    $starting_date = mysqli_real_escape_string($db, $_POST['starting_date']);
    $ending_date = mysqli_real_escape_string($db, $_POST['ending_date']);
    $status = mysqli_real_escape_string($db, $_POST['status']);

    $query = "UPDATE elections SET 
                election_topic = '$election_topic', 
                no_of_candidates = $number_of_candidates, 
                starting_date = '$starting_date', 
                ending_date = '$ending_date', 
                status = '$status' 
              WHERE id = $id";

    if (mysqli_query($db, $query)) {
        header("Location: edit_election.php?id=$id&updated=1");
    } else {
        echo "Error updating record: " . mysqli_error($db);
    }
} else {
    header("Location: ../index.php?addElectionPage=1");
}
?>
