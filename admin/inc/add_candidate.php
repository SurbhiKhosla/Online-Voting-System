<?php
include_once 'config.php';
include_once 'upload_image.php'; // Include the common image upload file

$message = "";

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['addCandidateBtn'])) {
    $election_id = mysqli_real_escape_string($db, $_POST['election_id']);
    $candidate_name = mysqli_real_escape_string($db, $_POST['candidate_name']);
    $candidate_details = mysqli_real_escape_string($db, $_POST['candidate_details']);
    $inserted_by = $_SESSION['username'];
    $inserted_on = date("Y-m-d");

    // Photograph Logic
    $uploadResult = handleImageUpload($_FILES['candidate_photo']);
    if ($uploadResult === "large") {
        $message = '<div class="alert alert-danger my-3" role="alert">Candidate image is too large, please upload a smaller file (you can upload any image up to 2 MB).</div>';
    } elseif ($uploadResult === "invalid") {
        $message = '<div class="alert alert-danger my-3" role="alert">Invalid image type (Only .jpg, .png files are allowed).</div>';
    } elseif ($uploadResult === "failed") {
        $message = '<div class="alert alert-danger my-3" role="alert">Image uploading failed, please try again.</div>';
    } else {
        $candidate_photo_name = $uploadResult;
        // Inserting into the database
        $query = "INSERT INTO candidate_details (election_id, candidate_name, candidate_details, candidate_photo, inserted_by, inserted_on) 
                  VALUES ('$election_id', '$candidate_name', '$candidate_details', '$candidate_photo_name', '$inserted_by', '$inserted_on')";
        mysqli_query($db, $query) or die(mysqli_error($db));

        $message = '<div class="alert alert-success my-3" role="alert">Candidate has been added successfully.</div>';
    }
}

if (isset($_GET['delete_id'])) {
    $d_id = $_GET['delete_id'];
    mysqli_query($db, "DELETE FROM candidate_details WHERE id = '$d_id'") or die(mysqli_error($db));
    $message = '<div class="alert alert-danger my-3" role="alert">Candidate has been deleted successfully!</div>';
}
?>

<div class="row my-3">
    <div class="col-4">
        <h3>Add New Candidate</h3>
        <?php echo $message; ?>
        <form method="POST" enctype="multipart/form-data">
            <div class="form-group">
                <select class="form-control" name="election_id" required>
                    <option value="">Select Election</option>
                    <?php 
                    $fetchingElections = mysqli_query($db, "SELECT * FROM elections") or die(mysqli_error($db));
                    $isAnyElectionAdded = mysqli_num_rows($fetchingElections);
                    if ($isAnyElectionAdded > 0) {
                        while ($row = mysqli_fetch_assoc($fetchingElections)) {
                            $election_id = $row['id'];
                            $election_name = $row['election_topic'];
                            $allowed_candidates = $row['no_of_candidates'];

                            // Checking how many candidates are added in this election
                            $fetchingCandidate = mysqli_query($db, "SELECT * FROM candidate_details WHERE election_id = '$election_id'") or die(mysqli_error($db));
                            $added_candidates = mysqli_num_rows($fetchingCandidate);

                            if ($added_candidates < $allowed_candidates) {
                    ?>
                                <option value="<?php echo $election_id; ?>"><?php echo $election_name; ?></option>
                    <?php
                            }
                        }
                    } else {
                    ?>
                        <option value="">Please add an election first</option>
                    <?php
                    }
                    ?>
                </select>
            </div>
            <div class="form-group">
                <input type="text" name="candidate_name" placeholder="Candidate Name" class="form-control" required />
            </div>
            <div class="form-group">
                <input type="file" name="candidate_photo" class="form-control" required />
            </div>
            <div class="form-group">
                <input type="text" name="candidate_details" placeholder="Candidate Details" class="form-control" required />
            </div>
            <input type="submit" value="Add Candidate" name="addCandidateBtn" class="btn btn-success" />
        </form>
    </div>

    <div class="col-8">
        <h3>Candidate Details</h3>
        <table class="table">
            <thead>
                <tr>
                    <th scope="col">S.No</th>
                    <th scope="col">Photo</th>
                    <th scope="col">Name</th>
                    <th scope="col">Details</th>
                    <th scope="col">Election</th>
                    <th scope="col">Action</th>
                </tr>
            </thead>
            <tbody>
                <?php 
                $fetchingData = mysqli_query($db, "SELECT * FROM candidate_details") or die(mysqli_error($db));
                $isAnyCandidateAdded = mysqli_num_rows($fetchingData);

                if ($isAnyCandidateAdded > 0) {
                    $sno = 1;
                    while ($row = mysqli_fetch_assoc($fetchingData)) {
                        $election_id = $row['election_id'];
                        $fetchingElectionName = mysqli_query($db, "SELECT election_topic FROM elections WHERE id = '$election_id'") or die(mysqli_error($db));
                        $execFetchingElectionNameQuery = mysqli_fetch_assoc($fetchingElectionName);
                        $election_name = $execFetchingElectionNameQuery['election_topic'];

                        $candidate_photo = "../assets/images/candidate_photos/" . $row['candidate_photo'];
                ?>
                        <tr>
                            <td><?php echo $sno++; ?></td>
                            <td><img src="<?php echo $candidate_photo; ?>" class="candidate_photo" alt="Candidate Photo" /></td>
                            <td><?php echo $row['candidate_name']; ?></td>
                            <td><?php echo $row['candidate_details']; ?></td>
                            <td><?php echo $election_name; ?></td>
                            <td>
                                <a href="http://localhost/OnlineVotingSystem/admin/inc/edit_candidate.php?id=<?php echo $row['id']; ?>" class="btn btn-sm btn-warning">Edit</a>
                                <button class="btn btn-sm btn-danger" onclick="DeleteData(<?php echo $row['id']; ?>)">Delete</button>
                            </td>
                        </tr>
                <?php
                    }
                } else {
                ?>
                    <tr>
                        <td colspan="6">No candidates have been added yet.</td>
                    </tr>
                <?php
                }
                ?>
            </tbody>
        </table>
    </div>
</div>

<script>
    const DeleteData = (id) => {
        let confirmDelete = confirm("Are you sure you want to delete this candidate?");
        if (confirmDelete) {
            location.assign("http://localhost/OnlineVotingSystem/admin/index.php?addCandidatePage=1&delete_id=" + id);
        }
    }
</script>
