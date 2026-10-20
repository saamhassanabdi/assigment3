
<?php
session_start();

$errors = [];
$old = [
    'name' => '', 'pob' => '', 'dob' => '', 'address' => '',
    'gender' => '', 'telephone' => '', 'email' => '',
    'parent_name' => '', 'parent_telephone' => '', 'relationship' => '',
    'student_id' => '', 'faculty' => '', 'department' => '',
    'class' => '', 'registration_date' => ''
];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    foreach ($old as $key => $value) {
        $old[$key] = trim($_POST[$key] ?? '');
    }

    $labels = [
        'name' => 'Name',
        'pob' => 'Place of Birth',
        'dob' => 'Date of Birth',
        'address' => 'Address',
        'gender' => 'Gender',
        'telephone' => 'Telephone',
        'email' => 'Email',
        'parent_name' => 'Parent Name',
        'parent_telephone' => 'Parent Telephone',
        'relationship' => 'Relationship',
        'student_id' => 'Student ID',
        'faculty' => 'Faculty',
        'department' => 'Department',
        'class' => 'Class',
        'registration_date' => 'Registration Date'
    ];

    foreach ($labels as $key => $label) {
        if ($old[$key] === '') {
            $errors[$key] = "Please enter/select " . $label . ".";
        }
    }

    if ($old['name'] !== '' &&
        !preg_match("/^[\p{L} ]+$/u", $old['name'])) {
        $errors['name'] = "Name must contain letters and spaces only.";
    }

    if ($old['email'] !== '' &&
        !filter_var($old['email'], FILTER_VALIDATE_EMAIL)) {
        $errors['email'] = "Please enter a valid email address.";
    }

    if (!isset($_FILES['image']) ||
        $_FILES['image']['error'] === UPLOAD_ERR_NO_FILE) {

        $errors['image'] = "Please upload a student image.";

    } elseif ($_FILES['image']['error'] !== UPLOAD_ERR_OK) {

        $errors['image'] = "The image could not be uploaded. Please try again.";

    } else {
        $maxSize = 2 * 1024 * 1024;

        if ($_FILES['image']['size'] > $maxSize) {
            $errors['image'] = "Image must be 2 MB or smaller.";
        } else {
            $finfo = new finfo(FILEINFO_MIME_TYPE);
            $mime = $finfo->file($_FILES['image']['tmp_name']);

            $allowed = [
                'image/jpeg' => 'jpg',
                'image/png' => 'png',
                'image/gif' => 'gif'
            ];

            if (!isset($allowed[$mime])) {
                $errors['image'] = "Only JPG, JPEG, PNG, or GIF images are allowed.";
            }
        }
    }

    if (!$errors) {
        $uploadDir = __DIR__ . '/uploads/';

        if (!is_dir($uploadDir)) {
            mkdir($uploadDir, 0755, true);
        }

        $extension = $allowed[$mime];
        $imageName = bin2hex(random_bytes(12)) . '.' . $extension;
        $destination = $uploadDir . $imageName;

        if (!move_uploaded_file($_FILES['image']['tmp_name'], $destination)) {
            $errors['image'] = "Unable to save the uploaded image. Check the uploads folder permissions.";
        } else {
            $old['image'] = 'uploads/' . $imageName;
            $_SESSION['student'] = $old;

            header('Location: display.php');
            exit;
        }
    }
}

function e($value) {
    return htmlspecialchars((string)$value, ENT_QUOTES, 'UTF-8');
}
?>

<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <title>Student-Registration</title>

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="style.css" rel="stylesheet">
</head>

<body>



<main class="container py-5">
    <div class="form-shell mx-auto">

        <div class="mb-4">
            <p class="eyebrow mb-1">STUDENT SERVICES</p>
            <h1 class="h2 fw-bold">Student Registration</h1>
            <p class="text-secondary mb-0">
                Please complete every field below.
            </p>
        </div>

        <?php if ($errors): ?>
            <div class="alert alert-danger" role="alert">
                <strong>Please correct the following:</strong>
                <ul class="mb-0 mt-2">
                    <?php foreach ($errors as $message): ?>
                        <li><?= e($message) ?></li>
                    <?php endforeach; ?>
                </ul>
            </div>
        <?php endif; ?>

        <form method="post" action="index.php"
              enctype="multipart/form-data" novalidate>

            <fieldset class="form-section">
                <legend>1. Personal Information</legend>

                <div class="row g-3">

                    <div class="col-md-6">
                        <label class="form-label" for="name">Full Name *</label>
                        <input class="form-control <?= isset($errors['name']) ? 'is-invalid' : '' ?>"
                               id="name" name="name"
                               value="<?= e($old['name']) ?>" required autofocus>

                        <?php if (isset($errors['name'])): ?>
                            <div class="invalid-feedback"><?= e($errors['name']) ?></div>
                        <?php endif; ?>
                    </div>

                    <div class="col-md-6">
                        <label class="form-label" for="pob">Place of Birth (POB) *</label>
                        <input class="form-control" id="pob" name="pob"
                               value="<?= e($old['pob']) ?>" required>
                    </div>

                    <div class="col-md-6">
                        <label class="form-label" for="dob">Date of Birth (DOB) *</label>
                        <input type="date" class="form-control" id="dob"
                               name="dob" value="<?= e($old['dob']) ?>" required>
                    </div>

                    <div class="col-md-6">
                        <label class="form-label" for="address">Address *</label>
                        <input class="form-control" id="address" name="address"
                               value="<?= e($old['address']) ?>" required>
                    </div>

                    <div class="col-md-6">
                        <label class="form-label" for="gender">Gender *</label>
                        <select class="form-select" id="gender" name="gender" required>
                            <option value="">Choose gender</option>
                            <option value="Female" <?= $old['gender'] === 'Female' ? 'selected' : '' ?>>Female</option>
                            <option value="Male" <?= $old['gender'] === 'Male' ? 'selected' : '' ?>>Male</option>
                        </select>
                    </div>

                    <div class="col-md-6">
                        <label class="form-label" for="telephone">Telephone *</label>
                        <input type="tel" class="form-control" id="telephone"
                               name="telephone" value="<?= e($old['telephone']) ?>" required>
                    </div>

                    <div class="col-md-6">
                        <label class="form-label" for="email">Email *</label>
                        <input type="email"
                               class="form-control <?= isset($errors['email']) ? 'is-invalid' : '' ?>"
                               id="email" name="email"
                               value="<?= e($old['email']) ?>" required>

                        <?php if (isset($errors['email'])): ?>
                            <div class="invalid-feedback"><?= e($errors['email']) ?></div>
                        <?php endif; ?>
                    </div>

                    <div class="col-md-6">
                        <label class="form-label" for="image">
                            Student Image (JPG, PNG, GIF; max 2 MB) *
                        </label>

                        <input type="file"
                               class="form-control <?= isset($errors['image']) ? 'is-invalid' : '' ?>"
                               id="image" name="image"
                               accept=".jpg,.jpeg,.png,.gif,image/jpeg,image/png,image/gif"
                               required>

                        <?php if (isset($errors['image'])): ?>
                            <div class="invalid-feedback"><?= e($errors['image']) ?></div>
                        <?php endif; ?>
                    </div>

                </div>
            </fieldset>

            <fieldset class="form-section">
                <legend>2. Parent Information</legend>

                <div class="row g-3">

                    <div class="col-md-6">
                        <label class="form-label" for="parent_name">
                            Parent/Guardian Name *
                        </label>
                        <input class="form-control" id="parent_name"
                               name="parent_name"
                               value="<?= e($old['parent_name']) ?>" required>
                    </div>

                    <div class="col-md-6">
                        <label class="form-label" for="parent_telephone">
                            Parent Telephone *
                        </label>
                        <input type="tel" class="form-control"
                               id="parent_telephone" name="parent_telephone"
                               value="<?= e($old['parent_telephone']) ?>" required>
                    </div>

                    <div class="col-md-6">
                        <label class="form-label" for="relationship">Relationship *</label>
                        <select class="form-select" id="relationship"
                                name="relationship" required>
                            <option value="">Choose relationship</option>

                            <?php foreach (['Father', 'Mother', 'Guardian', 'Other'] as $option): ?>
                                <option value="<?= e($option) ?>"
                                    <?= $old['relationship'] === $option ? 'selected' : '' ?>>
                                    <?= e($option) ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>

                </div>
            </fieldset>

            <fieldset class="form-section">
                <legend>3. Academic Information</legend>

                <div class="row g-3">

                    <div class="col-md-6">
                        <label class="form-label" for="student_id">Student ID *</label>
                        <input class="form-control" id="student_id"
                               name="student_id"
                               value="<?= e($old['student_id']) ?>" required>
                    </div>

                    <div class="col-md-6">
                        <label class="form-label" for="faculty">Faculty *</label>
                        <input class="form-control" id="faculty" name="faculty"
                               value="<?= e($old['faculty']) ?>" required>
                    </div>

                    <div class="col-md-6">
                        <label class="form-label" for="department">Department *</label>
                        <input class="form-control" id="department"
                               name="department"
                               value="<?= e($old['department']) ?>" required>
                    </div>

                    <div class="col-md-6">
                        <label class="form-label" for="class">Class *</label>
                        <input class="form-control" id="class" name="class"
                               value="<?= e($old['class']) ?>" required>
                    </div>

                    <div class="col-md-6">
                        <label class="form-label" for="registration_date">
                            Registration Date *
                        </label>
                        <input type="date" class="form-control"
                               id="registration_date" name="registration_date"
                               value="<?= e($old['registration_date']) ?>" required>
                    </div>

                </div>
            </fieldset>

            <div class="d-flex flex-column flex-sm-row gap-2 pt-2">
                <button class="btn btn-primary btn-lg px-4" type="submit">
                    Register Student
                </button>

                <button class="btn btn-outline-secondary btn-lg" type="reset">
                    Clear Form
                </button>
            </div>

            <p class="small text-secondary mt-3 mb-0">
                * Required fields
            </p>

        </form>
    </div>
</main>

<footer class="text-center py-4 text-secondary small">
  | PHP &amp; MySQL — Chapter 4 Assignment (Forms)
</footer>

</body>
</html>
