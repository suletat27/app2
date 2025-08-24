<?php
session_start();
if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit;
}

require_once 'config/database.php';
require_once 'models/Transaction.php';
require_once 'models/Customer.php';
require_once 'models/Payment.php';
require_once 'models/User.php';

$database = new Database();
$db = $database->getConnection();

$transaction = new Transaction($db);
$customer = new Customer($db);
$payment = new Payment($db);
$user = new User($db);

// Get user info from session
$user_id = $_SESSION['user_id'];
$user_role = $_SESSION['role'];

// Get data with user restrictions
$activeTransactions = $transaction->getActiveCount($user_id, $user_role);
$totalCustomers = $customer->getCount();
$pendingPayments = $payment->getPendingCount($user_id, $user_role);
$monthlyIncome = $payment->getMonthlyIncome($user_id, $user_role);

$recentTransactions = $transaction->getRecent(5, $user_id, $user_role);
$recentPayments = $payment->getRecent(5, $user_id, $user_role);
?>

<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard - PhoneGuard</title>
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
                            <h1>Dashboard</h1>
                        </div>
                        <div class="col-sm-6">
                            <ol class="breadcrumb float-sm-right">
                                <li class="breadcrumb-item"><a href="index.php">Home</a></li>
                                <li class="breadcrumb-item active">Dashboard</li>
                            </ol>
                        </div>
                    </div>
                </div>
            </section>

            <!-- Main content -->
            <section class="content">
                <div class="container-fluid">
                    <!-- Small boxes (Stat box) -->
                    <div class="row">
                        <div class="col-lg-3 col-6">
                            <div class="small-box bg-info">
                                <div class="inner">
                                    <h3><?php echo $activeTransactions; ?></h3>
                                    <p>Transaksi Aktif</p>
                                </div>
                                <div class="icon">
                                    <i class="fas fa-mobile-alt"></i>
                                </div>
                                <a href="transactions.php" class="small-box-footer">More info <i
                                        class="fas fa-arrow-circle-right"></i></a>
                            </div>
                        </div>
                        <div class="col-lg-3 col-6">
                            <div class="small-box bg-success">
                                <div class="inner">
                                    <h3>Rp <?php echo number_format($monthlyIncome, 0, ',', '.'); ?></h3>
                                    <p>Pendapatan Bulan Ini</p>
                                </div>
                                <div class="icon">
                                    <i class="fas fa-money-bill-wave"></i>
                                </div>
                                <a href="payments.php" class="small-box-footer">More info <i
                                        class="fas fa-arrow-circle-right"></i></a>
                            </div>
                        </div>
                        <div class="col-lg-3 col-6">
                            <div class="small-box bg-warning">
                                <div class="inner">
                                    <h3><?php echo $totalCustomers; ?></h3>
                                    <p>Pelanggan Terdaftar</p>
                                </div>
                                <div class="icon">
                                    <i class="fas fa-users"></i>
                                </div>
                                <a href="customers.php" class="small-box-footer">More info <i
                                        class="fas fa-arrow-circle-right"></i></a>
                            </div>
                        </div>
                        <div class="col-lg-3 col-6">
                            <div class="small-box bg-danger">
                                <div class="inner">
                                    <h3><?php echo $pendingPayments; ?></h3>
                                    <p>Pembayaran Tertunda</p>
                                </div>
                                <div class="icon">
                                    <i class="fas fa-exclamation-circle"></i>
                                </div>
                                <a href="payments.php" class="small-box-footer">More info <i
                                        class="fas fa-arrow-circle-right"></i></a>
                            </div>
                        </div>
                    </div>

                    <!-- Main row -->
                    <div class="row">
                        <section class="col-lg-7 connectedSortable">
                            <div class="card">
                                <div class="card-header">
                                    <h3 class="card-title">Transaksi Terbaru</h3>
                                </div>
                                <div class="card-body p-0">
                                    <table class="table table-striped">
                                        <thead>
                                            <tr>
                                                <th>No. Transaksi</th>
                                                <th>Pelanggan</th>
                                                <th>Tanggal</th>
                                                <th>Status</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <?php if (!empty($recentTransactions)): ?>
                                                <?php foreach ($recentTransactions as $transaction): ?>
                                                    <tr>
                                                        <td><a
                                                                href="transaction_detail.php?id=<?php echo $transaction['id']; ?>">TRX-<?php echo str_pad($transaction['id'], 4, '0', STR_PAD_LEFT); ?></a>
                                                        </td>
                                                        <td><?php echo $transaction['customer_name']; ?></td>
                                                        <td><?php echo date('d M Y', strtotime($transaction['start_date'])); ?>
                                                        </td>
                                                        <td>
                                                            <?php if ($transaction['status'] == 'active'): ?>
                                                                <span class="badge bg-success">Aktif</span>
                                                            <?php elseif ($transaction['status'] == 'completed'): ?>
                                                                <span class="badge bg-info">Selesai</span>
                                                            <?php else: ?>
                                                                <span class="badge bg-danger">Dibatalkan</span>
                                                            <?php endif; ?>
                                                        </td>
                                                    </tr>
                                                <?php endforeach; ?>
                                            <?php else: ?>
                                                <tr>
                                                    <td colspan="4" class="text-center">Tidak ada transaksi</td>
                                                </tr>
                                            <?php endif; ?>
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </section>

                        <section class="col-lg-5 connectedSortable">
                            <div class="card">
                                <div class="card-header">
                                    <h3 class="card-title">Pembayaran Terbaru</h3>
                                </div>
                                <div class="card-body p-0">
                                    <ul class="products-list product-list-in-card pl-2 pr-2">
                                        <?php if (!empty($recentPayments)): ?>
                                            <?php foreach ($recentPayments as $payment): ?>
                                                <li class="item">
                                                    <div class="product-info">
                                                        <a href="javascript:void(0)" class="product-title">
                                                            TRX-<?php echo str_pad($payment['transaction_id'], 4, '0', STR_PAD_LEFT); ?>
                                                            - <?php echo $payment['customer_name']; ?>
                                                            <span class="badge badge-success float-right">Rp
                                                                <?php echo number_format($payment['amount'], 0, ',', '.'); ?></span>
                                                        </a>
                                                        <span class="product-description">
                                                            <?php echo $payment['device_brand']; ?>
                                                            <?php echo $payment['device_type']; ?> -
                                                            <?php echo date('d M Y', strtotime($payment['payment_date'])); ?>
                                                        </span>
                                                    </div>
                                                </li>
                                            <?php endforeach; ?>
                                        <?php else: ?>
                                            <li class="item">
                                                <div class="product-info">
                                                    <span class="product-description">Tidak ada pembayaran</span>
                                                </div>
                                            </li>
                                        <?php endif; ?>
                                    </ul>
                                </div>
                            </div>
                        </section>
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