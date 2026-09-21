<?php

// =====================================================
// DashTech Computers - Admin Dashboard
// =====================================================

require_once __DIR__ . '/bootstrap.php';
requireAdmin();


// =====================================================
// SECURITY CHECK
// =====================================================

// =====================================================
// LOAD DATABASE
// =====================================================

require_once __DIR__ . '/../php/database.php';


// =====================================================
// GET BOOKING STATISTICS
// =====================================================

try {

    // Total bookings

    $stmt = $pdo->query("
        SELECT COUNT(*) 
        FROM bookings
    ");

    $totalBookings = (int) $stmt->fetchColumn();


    // Pending bookings

    $stmt = $pdo->query("
        SELECT COUNT(*) 
        FROM bookings
        WHERE status = 'Pending'
    ");

    $pendingBookings = (int) $stmt->fetchColumn();


    // Processing bookings

    $stmt = $pdo->query("
        SELECT COUNT(*) 
        FROM bookings
        WHERE status = 'Processing'
    ");

    $processingBookings = (int) $stmt->fetchColumn();


    // Completed bookings

    $stmt = $pdo->query("
        SELECT COUNT(*) 
        FROM bookings
        WHERE status = 'Completed'
    ");

    $completedBookings = (int) $stmt->fetchColumn();

    $stmt = $pdo->query("SELECT COUNT(*) FROM bookings WHERE status = 'Approved'");
    $approvedBookings = (int) $stmt->fetchColumn();

    $stmt = $pdo->query("SELECT COUNT(*) FROM bookings WHERE status = 'Cancelled'");
    $cancelledBookings = (int) $stmt->fetchColumn();


    // Recent bookings

    $stmt = $pdo->query("
        SELECT
            id,
            booking_reference,
            fullname,
            email,
            website_type,
            status,
            created_at
        FROM bookings
        ORDER BY created_at DESC
        LIMIT 10
    ");

    $recentBookings = $stmt->fetchAll();

} catch (PDOException $e) {

    error_log(
        'DashTech Dashboard Database Error: ' .
        $e->getMessage()
    );

    die(
        'Unable to load dashboard information.'
    );

}

?>

<!DOCTYPE html>

<html lang="en">

<head>

    <meta charset="UTF-8">

    <meta
        name="viewport"
        content="width=device-width, initial-scale=1.0"
    >

    <title>
        Admin Dashboard | DashTech Computers
    </title>

    <style>
        *{box-sizing:border-box} body{margin:0;background:#f4f7fb;color:#18212d;font:16px Arial,sans-serif} header{padding:24px 5%;background:#003366;color:#fff}header h1,header p{margin:0}header p{margin-top:5px;opacity:.85}nav{padding:14px 5%;background:#fff;box-shadow:0 1px 5px #00000012}nav a{margin-right:18px;color:#003366;font-weight:bold;text-decoration:none}main{max-width:1200px;margin:34px auto;padding:0 20px}section{margin:25px 0;padding:24px;background:#fff;border-radius:14px;box-shadow:0 4px 18px #00336612}section>div{display:inline-block;vertical-align:top;width:calc(33.333% - 14px);min-width:160px;margin:7px;padding:18px;border-left:4px solid #0077ff;background:#f7fbff;border-radius:6px}section h3,section p{margin:0}section div p{margin-top:10px;color:#003366;font-size:30px;font-weight:bold}table{width:100%;border-collapse:collapse}th,td{padding:12px;text-align:left;border-bottom:1px solid #e4e9ef}th{color:#52606d;font-size:13px;text-transform:uppercase}footer{text-align:center;padding:24px;color:#607080}@media(max-width:700px){main{margin:20px auto;padding:0 12px}section{padding:16px;overflow-x:auto}section>div{width:100%;margin:6px 0}table{min-width:700px}}
    </style>

</head>

<body>

<header>

    <h1>DashTech Computers</h1>

    <p>
        Administrator Dashboard
    </p>

</header>


<nav>

    <a href="dashboard.php">
        Dashboard
    </a>

    |

    <a href="bookings.php">
        Bookings
    </a>

    |

    <a href="logout.php">
        Logout
    </a>

</nav>


<main>

    <h2>
        Welcome,
        <?php
        echo htmlspecialchars(
            $_SESSION['admin_username'] ?? 'Admin'
        );
        ?>
    </h2>


    <!-- =================================================
         STATISTICS
    ================================================== -->

    <section>

        <h2>Booking Statistics</h2>

        <div>

            <h3>Total Bookings</h3>

            <p>
                <?php echo $totalBookings; ?>
            </p>

        </div>


        <div>

            <h3>Pending</h3>

            <p>
                <?php echo $pendingBookings; ?>
            </p>

        </div>


        <div>

            <h3>Processing</h3>

            <p>
                <?php echo $processingBookings; ?>
            </p>

        </div>


        <div>

            <h3>Completed</h3>

            <p>
                <?php echo $completedBookings; ?>
            </p>

        </div>

        <div>
            <h3>Approved</h3>
            <p><?php echo $approvedBookings; ?></p>
        </div>

        <div>
            <h3>Cancelled</h3>
            <p><?php echo $cancelledBookings; ?></p>
        </div>

    </section>

    <section>
        <h2>Quick actions</h2>
        <p><a href="bookings.php?status=Pending">Review pending bookings (<?php echo $pendingBookings; ?>)</a> · <a href="bookings.php">Open booking management</a></p>
    </section>


    <!-- =================================================
         RECENT BOOKINGS
    ================================================== -->

    <section>

        <h2>
            Recent Bookings
        </h2>

        <?php if (empty($recentBookings)): ?>

            <p>
                No bookings found.
            </p>

        <?php else: ?>

            <table
                border="1"
                cellpadding="8"
                cellspacing="0"
            >

                <thead>

                    <tr>
                        <th>
                            Reference
                        </th>

                        <th>
                            Customer
                        </th>

                        <th>
                            Email
                        </th>

                        <th>
                            Website
                        </th>

                        <th>
                            Status
                        </th>

                        <th>
                            Date
                        </th>

                        <th>
                            Action
                        </th>

                    </tr>

                </thead>


                <tbody>

                <?php foreach (
                    $recentBookings
                    as $booking
                ): ?>

                    <tr>

                        <td>
                            <?php
                            echo htmlspecialchars(
                                $booking['booking_reference']
                            );
                            ?>
                        </td>


                        <td>
                            <?php
                            echo htmlspecialchars(
                                $booking['fullname']
                            );
                            ?>
                        </td>


                        <td>
                            <?php
                            echo htmlspecialchars(
                                $booking['email']
                            );
                            ?>
                        </td>


                        <td>
                            <?php
                            echo htmlspecialchars(
                                $booking['website_type']
                            );
                            ?>
                        </td>


                        <td>
                            <?php
                            echo htmlspecialchars(
                                $booking['status']
                            );
                            ?>
                        </td>


                        <td>
                            <?php
                            echo htmlspecialchars(
                                $booking['created_at']
                            );
                            ?>
                        </td>


                        <td>

                            <a
                                href="view-booking.php?id=<?php echo (int) $booking['id']; ?>"
                            >
                                View
                            </a>

                        </td>

                    </tr>

                <?php endforeach; ?>

                </tbody>

            </table>

        <?php endif; ?>

    </section>

</main>


<footer>

    <p>
        &copy;
        <?php echo date('Y'); ?>
        DashTech Computers.
        All Rights Reserved.
    </p>

</footer>

</body>

</html>
