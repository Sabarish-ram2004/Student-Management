<?php
include "db.php";

$id = $_GET['id'];

$stmt = mysqli_prepare($conn, "SELECT * FROM students WHERE id=?");
mysqli_stmt_bind_param($stmt, "i", $id);
mysqli_stmt_execute($stmt);
$student = mysqli_fetch_assoc(mysqli_stmt_get_result($stmt));

if (isset($_POST['update'])) {
    $stmt = mysqli_prepare($conn,
        "UPDATE students SET name=?, email=?, course=? WHERE id=?"
    );
    mysqli_stmt_bind_param($stmt, "sssi",
        $_POST['name'], $_POST['email'], $_POST['course'], $id
    );
    mysqli_stmt_execute($stmt);
    header("Location: index.php");
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Edit Student</title>
    <link rel="stylesheet" href="assets/style.css">
</head>
<body>

<div class="container">
    <div class="card">
        <h1>Edit Student</h1>

        <form method="POST">
            <input type="text" name="name"
                   value="<?= htmlspecialchars($student['name']) ?>" required>
            <input type="email" name="email"
                   value="<?= htmlspecialchars($student['email']) ?>" required>
            <input type="text" name="course"
                   value="<?= htmlspecialchars($student['course']) ?>" required>
            <button name="update">Update Student</button>
        </form>

        <a class="back-link" href="index.php">⬅ Back</a>
    </div>
</div>

</body>
</html>
