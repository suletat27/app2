<?php
session_start();
if (!isset($_SESSION['user_id']) || $_SESSION['role'] != 'superadmin') {
    header('Location: index.php');
    exit;
}

require_once 'config/database.php';
require_once 'models/User.php';

$database = new Database();
$db = $database->getConnection();

$user = new User($db);
$message = '';

// Create new user
if ($_POST && isset($_POST['create'])) {
    $user->username = $_POST['username'];
    $user->password = $_POST['password'];
    $user->name = $_POST['name'];
    $user->role = $_POST['role'];

    // Check if username exists
    if ($user->usernameExists()) {
        $message = '<div class="alert alert-danger">Username sudah digunakan.</div>';
    } else {
        if ($user->create()) {
            $message = '<div class="alert alert-success">User berhasil dibuat.</div>';
        } else {
            $message = '<div class="alert alert-danger">Terjadi kesalahan.</div>';
        }
    }
}

// Get all users
$users = $user->readAll();
?>

<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <title>Manajemen User - PhoneGuard</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/admin-lte@3.2/dist/css/adminlte.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>

<body class="hold-transition sidebar-mini">
    <div class="wrapper">

        <!-- Header -->
        <?php include 'includes/header.php'; ?>

        <!-- Sidebar -->
        <?php include 'includes/sidebar.php'; ?>

        <!-- Content Wrapper. Contains page content -->
        <div class="content-wrapper">
            <!-- Content Header (Page header) -->
            <section class="content-header">
                <div class="container-fluid">
                    <div class="row mb-2">
                        <div class="col-sm-6">
                            <h1>Manajemen User</h1>
                        </div>
                        <div class="col-sm-6">
                            <ol class="breadcrumb float-sm-right">
                                <li class="breadcrumb-item"><a href="index.php">Home</a></li>
                                <li class="breadcrumb-item active">Manajemen User</li>
                            </ol>
                        </div>
                    </div>
                </div>
            </section>

            <!-- Main content -->
            <section class="content">
                <div class="container-fluid">
                    <?php echo $message; ?>

                    <div class="row">
                        <div class="col-md-6">
                            <div class="card card-primary">
                                <div class="card-header">
                                    <h3 class="card-title">Tambah User Baru</h3>
                                </div>
                                <form method="post">
                                    <div class="card-body">
                                        <div class="form-group">
                                            <label for="username">Username</label>
                                            <input type="text" class="form-control" id="username" name="username"
                                                required>
                                        </div>
                                        <div class="form-group">
                                            <label for="password">Password</label>
                                            <input type="password" class="form-control" id="password" name="password"
                                                required>
                                        </div>
                                        <div class="form-group">
                                            <label for="name">Nama Lengkap</label>
                                            <input type="text" class="form-control" id="name" name="name" required>
                                        </div>
                                        <div class="form-group">
                                            <label for="role">Role</label>
                                            <select class="form-control" id="role" name="role">
                                                <option value="user">User</option>
                                                <option value="superadmin">Super Admin</option>
                                            </select>
                                        </div>
                                    </div>
                                    <div class="card-footer">
                                        <button type="submit" name="create" class="btn btn-primary">Simpan</button>
                                    </div>
                                </form>
                            </div>
                        </div>

                        <div class="col-md-6">
                            <div class="card">
                                <div class="card-header">
                                    <h3 class="card-title">Daftar User</h3>
                                </div>
                                <div class="card-body">
                                    <table class="table table-bordered">
                                        <thead>
                                            <tr>
                                                <th>Username</th>
                                                <th>Nama</th>
                                                <th>Role</th>
                                                <th>Tanggal Dibuat</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <?php while ($row = $users->fetch(PDO::FETCH_ASSOC)): ?>
                                                <tr>
                                                    <td><?php echo $row['username']; ?></td>
                                                    <td><?php echo $row['name']; ?></td>
                                                    <td>
                                                        <?php if ($row['role'] == 'superadmin'): ?>
                                                            <span class="badge bg-danger">Super Admin</span>
                                                        <?php else: ?>
                                                            <span class="badge bg-primary">User</span>
                                                        <?php endif; ?>
                                                    </td>
                                                    <td><?php echo date('d M Y', strtotime($row['created_at'])); ?></td>
                                                </tr>
                                            <?php endwhile; ?>
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </section>
        </div>

        <!-- Footer -->
        <?php include 'includes/footer.php'; ?>
    </div>

    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@4.6.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/admin-lte@3.2/dist/js/adminlte.min.js"></script>
</body>

</html>