<?php
session_start();

if (empty($_SESSION['student']) || !is_array($_SESSION['student'])) {
    header('Location: index.php');
    exit;
}

$student = $_SESSION['student'];

function e($value) {
    return htmlspecialchars((string)$value, ENT_QUOTES, 'UTF-8');
}

$rows = [
    'Name' => 'name',
    'Place of Birth' => 'pob',
    'Date of Birth' => 'dob',
    'Address' => 'address',
    'Gender' => 'gender',
    'Telephone' => 'telephone',
    'Email' => 'email',
    'Parent/Guardian Name' => 'parent_name',
    'Parent Telephone' => 'parent_telephone',
    'Relationship' => 'relationship',
    'Student ID' => 'student_id',
    'Faculty' => 'faculty',
    'Department' => 'department',
    'Class' => 'class',
    'Registration Date' => 'registration_date'
];
?>
<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Student Information</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="style.css" rel="stylesheet">
</head>
<body>

<main class="container py-5">
    <div class="result-shell mx-auto">
        <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center gap-3 mb-4">
            <div>
                <p class="eyebrow mb-1">REGISTRATION COMPLETE</p>
                <h1 class="h2 fw-bold mb-1">Student Information</h1>
                <p class="text-secondary mb-0">The submitted information is shown below.</p>
            </div>
            <a href="index.php" class="btn btn-outline-primary">Register Another Student</a>
        </div>

        <div class="text-center mb-4">
            <?php if (!empty($student['image']) && is_file(__DIR__ . '/' . $student['image'])): ?>
                <img src="<?= e($student['image']) ?>" alt="Student image" class="student-photo">
            <?php endif; ?>
            <h2 class="h5 mt-3 mb-0"><?= e($student['name'] ?? '') ?></h2>
            <p class="text-secondary small">Student ID: <?= e($student['student_id'] ?? '') ?></p>
        </div>

        <div class="table-responsive">
            <table class="table table-bordered table-striped align-middle">
                <thead class="table-dark">
                    <tr><th scope="col" style="width: 38%">Information Field</th><th scope="col">Submitted Information</th></tr>
                </thead>
                <tbody>
                    <?php foreach ($rows as $label => $key): ?>
                        <tr>
                            <th scope="row"><?= e($label) ?></th>
                            <td><?= e($student[$key] ?? '') ?></td>
                        </tr>
                    <?php endforeach; ?>
                    <tr>
                        <th scope="row">Student Image</th>
                        <td><?= !empty($student['image']) ? 'Uploaded successfully' : 'No image' ?></td>
                    </tr>
                </tbody>
            </table>
        </div>
        <div class="alert alert-success mt-4 mb-0">Student information was submitted successfully.</div>
    </div>
</main>
<footer class="text-center py-4 text-secondary small">PHP &amp; MySQL — Chapter 4 Assignment (Forms)</footer>
</body>
</html>
