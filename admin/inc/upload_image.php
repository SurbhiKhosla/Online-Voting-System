<?php
function handleImageUpload($file) {
    $targetted_folder = "../assets/images/candidate_photos/";
    $candidate_photo_name = basename($file['name']);
    $candidate_photo = $targetted_folder . $candidate_photo_name;
    $candidate_photo_tmp_name = $file['tmp_name'];
    $candidate_photo_type = strtolower(pathinfo($candidate_photo, PATHINFO_EXTENSION));
    $allowed_types = array("jpg", "png", "jpeg");
    $image_size = $file['size'];

    if ($image_size < 2000000) { // 2 MB
        if (in_array($candidate_photo_type, $allowed_types)) {
            if (move_uploaded_file($candidate_photo_tmp_name, $candidate_photo)) {
                return $candidate_photo_name;
            } else {
                return "failed";
            }
        } else {
            return "invalid";
        }
    } else {
        return "large";
    }
}
?>
