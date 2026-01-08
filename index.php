<?php
include "db.php";

$message = "";

/* ADD STUDENT */
if (isset($_POST['add'])) {
    if (!empty($_POST['name']) && !empty($_POST['email']) && !empty($_POST['course'])) {
        $stmt = mysqli_prepare($conn,
            "INSERT INTO students (name, email, course) VALUES (?, ?, ?)"
        );
        mysqli_stmt_bind_param($stmt, "sss",
            $_POST['name'], $_POST['email'], $_POST['course']
        );

        if (mysqli_stmt_execute($stmt)) {
            $message = "<div class='success'>Student added successfully</div>";
        } else {
            $message = "<div class='error'>Failed to add student</div>";
        }
    } else {
        $message = "<div class='error'>All fields are required</div>";
    }
}

/* SEARCH + PAGINATION */
$search = $_GET['search'] ?? "";
$page = $_GET['page'] ?? 1;
$limit = 5;
$start = ($page - 1) * $limit;

$stmt = mysqli_prepare($conn,
    "SELECT * FROM students
     WHERE name LIKE ? OR course LIKE ?
     LIMIT ?, ?"
);

$like = "%$search%";
mysqli_stmt_bind_param($stmt, "ssii", $like, $like, $start, $limit);
mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);

/* TOTAL COUNT */
$countStmt = mysqli_prepare($conn,
    "SELECT COUNT(*) AS total FROM students
     WHERE name LIKE ? OR course LIKE ?"
);
mysqli_stmt_bind_param($countStmt, "ss", $like, $like);
mysqli_stmt_execute($countStmt);
$total = mysqli_fetch_assoc(mysqli_stmt_get_result($countStmt))['total'];
$totalPages = ceil($total / $limit);
?>

<!DOCTYPE html>
<html>
<head>
    <title>Student Management System</title>
    <link rel="stylesheet" href="style.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-sRIl4kxILFvY47J16cr9ZwB07vP4J8+LH7qKQnuqkuIAvNWLzeN8tE5YBujZqJLB" crossorigin="anonymous">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/js/bootstrap.bundle.min.js" integrity="sha384-FKyoEForCGlyvwx9Hj09JcYn3nv7wiPVlz7YYwJrWVcXK/BmnVDxM+D2scQbITxI" crossorigin="anonymous"></script>
</head>
<body>

<div class="container">

    <h1>🎓 Student Management System</h1>

    <?= $message ?>

    <!-- ADD STUDENT -->
    <div class="card">
        <h2>Add Student</h2>
        <form method="POST">
            <input type="text" name="name" placeholder="Student Name">
            <input type="email" name="email" placeholder="Student Email">
            <input type="text" name="course" placeholder="Course Name">
            <button name="add">Add Student</button>
        </form>
    </div>

    <!-- SEARCH -->
    <form method="GET" class="search-form">
        <input type="text" name="search" placeholder="Search by name or course"
               value="<?= htmlspecialchars($search) ?>">
        <button>Search</button>
    </form>

    <!-- STUDENT TABLE -->
    <div class="card">
        <h2>Student List</h2>
        <table>
            <thead>
                <tr>
                    <th>ID</th><th>Name</th><th>Email</th><th>Course</th><th>Action</th>
                </tr>
            </thead>
            <tbody>
            <?php if (mysqli_num_rows($result) > 0): ?>
                <?php while ($row = mysqli_fetch_assoc($result)): ?>
                <tr>
                    <td><?= $row['id'] ?></td>
                    <td><?= htmlspecialchars($row['name']) ?></td>
                    <td><?= htmlspecialchars($row['email']) ?></td>
                    <td><?= htmlspecialchars($row['course']) ?></td>
                    <td class="actions">
                        <a class="edit" href="edit.php?id=<?= $row['id'] ?>">Edit</a>
                        <a class="delete"
                           href="delete.php?id=<?= $row['id'] ?>"
                           onclick="return confirm('Delete this student?')">
                           Delete
                        </a>
                    </td>
                </tr>
                <?php endwhile; ?>
            <?php else: ?>
                <tr><td colspan="5">No students found</td></tr>
            <?php endif; ?>
            </tbody>
        </table>
    </div>

    <!-- PAGINATION -->
    <div class="pagination">
        <?php for ($i = 1; $i <= $totalPages; $i++): ?>
            <a class="<?= ($i == $page) ? 'active' : '' ?>"
               href="?page=<?= $i ?>&search=<?= htmlspecialchars($search) ?>">
               <?= $i ?>
            </a>
        <?php endfor; ?>
    </div>

    
</div>

</body>
</html>
