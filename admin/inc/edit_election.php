<?php
// Include the database configuration file
include_once 'config.php';

// Get the election ID from the URL
$election_id = isset($_GET['id']) ? intval($_GET['id']) : 0;

if ($election_id > 0) {
    $result = mysqli_query($db, "SELECT * FROM elections WHERE id = $election_id") or die(mysqli_error($db));
    $election = mysqli_fetch_assoc($result);
} else {
    die("Invalid Election ID.");
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Edit Election</title>
    <!-- Bootstrap CSS -->
    <link href="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
    <div class="container mt-5">
        <h1 class="mb-4">Edit Election</h1>
        <?php if (isset($_GET['updated']) && $_GET['updated'] == 1): ?>
            <div class="alert alert-success" role="alert">
                Election details have been updated successfully!
            </div>
        <?php endif; ?>
        <?php if ($election): ?>
            <form method="POST" action="update_election.php" class="needs-validation" novalidate>
                <input type="hidden" name="id" value="<?php echo $election['id']; ?>">
                <div class="form-group">
                    <label for="election_topic">Election Topic:</label>
                    <input type="text" id="election_topic" name="election_topic" class="form-control" value="<?php echo $election['election_topic']; ?>" required>
                    <div class="invalid-feedback">
                        Please provide a topic for the election.
                    </div>
                </div>
                <div class="form-group">
                    <label for="number_of_candidates">Number of Candidates:</label>
                    <input type="number" id="number_of_candidates" name="number_of_candidates" class="form-control" value="<?php echo $election['no_of_candidates']; ?>" required>
                    <div class="invalid-feedback">
                        Please provide the number of candidates.
                    </div>
                </div>
                <div class="form-group">
                    <label for="starting_date">Starting Date:</label>
                    <input type="date" id="starting_date" name="starting_date" class="form-control" value="<?php echo $election['starting_date']; ?>" required>
                    <div class="invalid-feedback">
                        Please provide a starting date.
                    </div>
                </div>
                <div class="form-group">
                    <label for="ending_date">Ending Date:</label>
                    <input type="date" id="ending_date" name="ending_date" class="form-control" value="<?php echo $election['ending_date']; ?>" required>
                    <div class="invalid-feedback">
                        Please provide an ending date.
                    </div>
                </div>
                <div class="form-group">
                    <label for="status">Status:</label>
                    <input type="text" id="status" name="status" class="form-control" value="<?php echo $election['status']; ?>" required>
                    <div class="invalid-feedback">
                        Please provide a status.
                    </div>
                </div>
                <button type="submit" class="btn btn-primary">Update Election</button>
            </form>
        <?php else: ?>
            <div class="alert alert-danger" role="alert">
                Election not found.
            </div>
        <?php endif; ?>
    </div>

    <!-- Bootstrap JS and dependencies -->
    <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.5.4/dist/umd/popper.min.js"></script>
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
    <script>
        // Example starter JavaScript for disabling form submissions if there are invalid fields
        (function () {
            'use strict';
            window.addEventListener('load', function () {
                // Fetch all the forms we want to apply custom Bootstrap validation styles to
                var forms = document.getElementsByClassName('needs-validation');
                // Loop over them and prevent submission
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
