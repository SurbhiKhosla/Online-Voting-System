<?php
include_once 'config.php';
include_once 'upload_image.php'; // Include the common image upload file

// Get the candidate ID from the URL
$candidate_id = isset($_GET['id']) ? intval($_GET['id']) : 0;

if ($candidate_id > 0) {
    // Fetch candidate details from the database
    $query = "SELECT * FROM candidate_details WHERE id = $candidate_id";
    $result = mysqli_query($db, $query);
    $candidate = mysqli_fetch_assoc($result);
    if (!$candidate) {
        die("Candidate not found.");
    }
} else {
    die("Invalid Candidate ID.");
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id = intval($_POST['id']);
    $election_id = mysqli_real_escape_string($db, $_POST['election_id']);
    $candidate_name = mysqli_real_escape_string($db, $_POST['candidate_name']);
    $candidate_details = mysqli_real_escape_string($db, $_POST['candidate_details']);
    
    // Check if a new photo has been uploaded
    if ($_FILES['candidate_photo']['size'] > 0) {
        $uploadResult = handleImageUpload($_FILES['candidate_photo']);
        if ($uploadResult === "large") {
            echo "<script>location.assign('edit_candidate.php?id=$id&largeFile=1');</script>";
            exit;
        } elseif ($uploadResult === "invalid") {
            echo "<script>location.assign('edit_candidate.php?id=$id&invalidFile=1');</script>";
            exit;
        } elseif ($uploadResult === "failed") {
            echo "<script>location.assign('edit_candidate.php?id=$id&failed=1');</script>";
            exit;
        } else {
            $candidate_photo_name = $uploadResult;
        }
    } else {
        $candidate_photo_name = $candidate['candidate_photo'];
    }
    
    // Update candidate details in the database
    $query = "UPDATE candidate_details 
              SET election_id = '$election_id', candidate_name = '$candidate_name', candidate_details = '$candidate_details', candidate_photo = '$candidate_photo_name' 
              WHERE id = $id";
    mysqli_query($db, $query) or die(mysqli_error($db));
    
    echo "<script>location.assign('edit_candidate.php?id=$id&updated=1');</script>";
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Candidate</title>
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <style>
        .candidate_photo {
            width: 50px;
            height: 50px;
            border-radius: 50%;
        }
    </style>
</head>
<body>
    <div class="container mt-5">
        <h1 class="mb-4">Edit Candidate</h1>
        <?php if (isset($_GET['updated']) && $_GET['updated'] == 1): ?>
            <div class="alert alert-success" role="alert">
                Candidate details have been updated successfully!
            </div>
        <?php endif; ?>
        <form method="POST" action="edit_candidate.php?id=<?php echo $candidate_id; ?>" class="needs-validation" novalidate enctype="multipart/form-data">
            <input type="hidden" name="id" value="<?php echo htmlspecialchars($candidate['id']); ?>">
            <div class="form-group">
                <label for="election_id">Election:</label>
                <select class="form-control" id="election_id" name="election_id" required>
                    <?php
                    $fetchingElections = mysqli_query($db, "SELECT * FROM elections") or die(mysqli_error($db));
                    while ($row = mysqli_fetch_assoc($fetchingElections)) {
                        $selected = $row['id'] == $candidate['election_id'] ? 'selected' : '';
                        echo "<option value='{$row['id']}' $selected>{$row['election_topic']}</option>";
                    }
                    ?>
                </select>
                <div class="invalid-feedback">
                    Please select an election.
                </div>
            </div>
            <div class="form-group">
                <label for="candidate_name">Candidate Name:</label>
                <input type="text" id="candidate_name" name="candidate_name" class="form-control" value="<?php echo htmlspecialchars($candidate['candidate_name']); ?>" required>
                <div class="invalid-feedback">
                    Please provide a candidate name.
                </div>
            </div>
            <div class="form-group">
                <label for="candidate_details">Candidate Details:</label>
                <textarea id="candidate_details" name="candidate_details" class="form-control" required><?php echo htmlspecialchars($candidate['candidate_details']); ?></textarea>
                <div class="invalid-feedback">
                    Please provide candidate details.
                </div>
            </div>
            <div class="form-group">
                <label for="candidate_photo">Candidate Photo:</label>
                <input type="file" id="candidate_photo" name="candidate_photo" class="form-control">
                <?php if (!empty($candidate['candidate_photo'])): ?>
                    <small class="form-text text-muted">Current photo:</small>
                    <?php 
                        $photo_path = "../../assets/images/candidate_photos/" . htmlspecialchars($candidate['candidate_photo']);
                        // echo "<p>Checking path: $photo_path</p>"; // Debugging line
                        if (file_exists($photo_path)) {
                            echo "<img src='$photo_path' class='candidate_photo' alt='Candidate Photo'>";
                        } else {
                            echo "<small class='form-text text-muted'>Photo file not found at $photo_path.</small>";
                        }
                    ?>
                <?php else: ?>
                    <small class="form-text text-muted">No photo available.</small>
                <?php endif; ?>
            </div>
            <button type="submit" class="btn btn-primary">Update Candidate</button>
        </form>
    </div>

    <!-- Bootstrap JS and dependencies -->
    <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.5.4/dist/umd/popper.min.js"></script>
    <script src="https://maxcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
    <script>
        (function () {
            'use strict';
            window.addEventListener('load', function () {
                var forms = document.getElementsByClassName('needs-validation');
                var validation = Array.prototype.filter.call(forms, function (form) {
                    form.addEventListener('submit', function (event) {
                        if (form.checkValidity() === false) {
                            event.preventDefault();
                            event.stopPropagation();
                        }
                        form.classList.add('was-validated');
                    }, false);
                });
            }, false);
        })();
    </script>
</body>
</html>
