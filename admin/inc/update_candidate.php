<?php
include_once 'config.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $id = intval($_POST['id']);
    $election_id = intval($_POST['election_id']);
    $candidate_name = mysqli_real_escape_string($db, $_POST['candidate_name']);
    $candidate_details = mysqli_real_escape_string($db, $_POST['candidate_details']);

    // Handle photo upload
    $candidate_photo = null;
    if (!empty($_FILES['candidate_photo']['name'])) {
        $targetted_folder = "../assets/images/candidate_photos/";

        // Check if the directory exists, if not create it
        if (!is_dir($targetted_folder)) {
            mkdir($targetted_folder, 0777, true);
        }

        $candidate_photo = $targetted_folder . rand(111111111, 99999999999) . "_" . rand(111111111, 99999999999) . basename($_FILES['candidate_photo']['name']);
        $candidate_photo_tmp_name = $_FILES['candidate_photo']['tmp_name'];
        $candidate_photo_type = strtolower(pathinfo($candidate_photo, PATHINFO_EXTENSION));
        $allowed_types = array("jpg", "png", "jpeg");
        $image_size = $_FILES['candidate_photo']['size'];

        if ($image_size < 2000000) { // 2 MB
            if (in_array($candidate_photo_type, $allowed_types)) {
                if (!move_uploaded_file($candidate_photo_tmp_name, $candidate_photo)) {
                    die("Failed to upload the photo.");
                }
            } else {
                die("Invalid image type. Only .jpg, .png files are allowed.");
            }
        } else {
            die("Image is too large. Please upload an image up to 2MB.");
        }
    }

    // Prepare query
    if ($candidate_photo) {
        $query = "UPDATE candidate_details SET 
                    election_id = $election_id, 
                    candidate_name = '$candidate_name', 
                    candidate_details = '$candidate_details',
                    candidate_photo = '$candidate_photo'
                  WHERE id = $id";
    } else {
        $query = "UPDATE candidate_details SET 
                    election_id = $election_id, 
                    candidate_name = '$candidate_name', 
                    candidate_details = '$candidate_details'
                  WHERE id = $id";
    }

    if (mysqli_query($db, $query)) {
        header("Location: edit_candidate.php?id=$election_id&updated=1");
    } else {
        echo "Error updating record: " . mysqli_error($db);
    }
} else {
    header("Location: ../index.php?addCandidatePage=1");
}
?>
