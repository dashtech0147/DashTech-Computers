<?php

// =====================================================
// DashTech Computers - View Booking
// =====================================================

require_once __DIR__ . '/bootstrap.php';
requireAdmin();


// =====================================================
// ADMIN SECURITY CHECK
// =====================================================

// =====================================================
// LOAD DATABASE
// =====================================================

require_once __DIR__ . '/../php/database.php';


// =====================================================
// GET BOOKING ID
// =====================================================

$id = filter_input(
    INPUT_GET,
    'id',
    FILTER_VALIDATE_INT
);

if (!$id) {

    die('Invalid booking ID.');

}


// =====================================================
// FETCH BOOKING
// =====================================================

try {

    $stmt = $pdo->prepare("
        SELECT
            id,
            booking_reference,
            fullname,
            email,
            phone,
            company,
            website_type,
            project_details,
            attachment,
            status,
            created_at
        FROM bookings
        WHERE id = ?
        LIMIT 1
    ");

    $stmt->execute([$id]);

    $booking = $stmt->fetch();

} catch (PDOException $e) {

    error_log(
        'DashTech View Booking Error: ' .
        $e->getMessage()
    );

    die(
        'Unable to load this booking.'
    );

}


// =====================================================
// CHECK IF BOOKING EXISTS
// =====================================================

if (!$booking) {

    die('Booking not found.');

}


// =====================================================
// SAFE STATUS CLASS
// =====================================================

$statusClass =
    'status-' .
    strtolower(
        preg_replace(
            '/[^a-zA-Z0-9]+/',
            '-',
            $booking['status']
        )
    );

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
        <?php
        echo htmlspecialchars(
            $booking['booking_reference']
        );
        ?>
        | DashTech Admin
    </title>


    <style>

        * {
            box-sizing: border-box;
        }


        body {

            margin: 0;

            font-family:
                Arial,
                Helvetica,
                sans-serif;

            background: #f4f6f9;

            color: #222;

        }


        /* =================================================
           HEADER
        ================================================== */

        header {

            background: #003366;

            color: #ffffff;

            padding: 20px;

        }

        header h1 {

            margin: 0;

            font-size: 24px;

        }

        header p {

            margin: 5px 0 0;

            opacity: .9;

        }


        /* =================================================
           NAVIGATION
        ================================================== */

        nav {

            background: #ffffff;

            padding: 15px 20px;

            border-bottom:
                1px solid #ddd;

        }

        nav a {

            color: #003366;

            text-decoration: none;

            font-weight: 600;

            margin-right: 20px;

        }

        nav a:hover {

            text-decoration: underline;

        }


        /* =================================================
           MAIN
        ================================================== */

        main {

            max-width: 1100px;

            margin: 30px auto;

            padding: 0 20px;

        }


        /* =================================================
           TOP BAR
        ================================================== */

        .top-bar {
            display: flex;

            justify-content:
                space-between;

            align-items: center;

            gap: 15px;

            margin-bottom: 25px;

        }

        .top-bar h2 {

            margin: 0;

        }


        .button {

            display: inline-block;

            padding: 10px 16px;

            border-radius: 6px;

            background: #003366;

            color: #ffffff;

            text-decoration: none;

            border: none;

            cursor: pointer;

        }

        .button:hover {

            opacity: .9;

        }


        .button.secondary {

            background: #666;

        }


        /* =================================================
           CARD
        ================================================== */

        .card {

            background: #ffffff;

            border-radius: 10px;

            padding: 25px;

            margin-bottom: 20px;

            box-shadow:
                0 2px 8px
                rgba(0,0,0,.06);

        }

        .card h3 {

            margin-top: 0;

            color: #003366;

            border-bottom:
                1px solid #eee;

            padding-bottom: 12px;

        }


        /* =================================================
           INFORMATION GRID
        ================================================== */

        .info-grid {

            display: grid;

            grid-template-columns:
                repeat(2, 1fr);

            gap: 18px;

        }

        .info-item {

            padding: 12px;

            background: #f8fafc;

            border-radius: 6px;

        }

        .info-label {

            display: block;

            font-size: 12px;

            color: #777;

            margin-bottom: 5px;

            text-transform:
                uppercase;

            font-weight: bold;

        }

        .info-value {

            font-size: 15px;

            word-break: break-word;

        }


        /* =================================================
           STATUS
        ================================================== */

        .status {

            display: inline-block;

            padding: 6px 12px;

            border-radius: 20px;

            font-size: 13px;

            font-weight: bold;

        }

        .status-pending {

            background: #fff3cd;

            color: #856404;

        }

        .status-processing {

            background: #cfe2ff;

            color: #084298;

        }

        .status-approved {

            background: #d1e7dd;

            color: #0f5132;

        }

        .status-completed {

            background: #198754;

            color: #ffffff;

        }

        .status-cancelled {

            background: #f8d7da;

            color: #842029;

        }


        /* =================================================
           PROJECT DETAILS
        ================================================== */

        .project-details {

            white-space: pre-wrap;

            line-height: 1.7;

            background: #f8fafc;

            padding: 18px;

            border-radius: 6px;

            min-height: 80px;

        }


        /* =================================================
           ATTACHMENT
        ================================================== */

        .attachment {

            padding: 15px;

            background: #f8fafc;

            border-radius: 6px;

        }

        .notice { padding: 12px 15px; margin-bottom: 20px; border-radius: 6px; background: #d1e7dd; color: #0f5132; }
        .status-form { display: flex; flex-wrap: wrap; gap: 10px; align-items: end; }
        .status-form label { display: grid; gap: 6px; font-weight: bold; }
        .status-form select { padding: 11px; border: 1px solid #ccc; border-radius: 6px; }


        /* =================================================
           MOBILE
        ================================================== */

        @media (
            max-width: 700px
        ) {

            main {

                margin-top: 20px;

                padding: 0 12px;

            }

            .top-bar {

                align-items:
                    flex-start;

                flex-direction:
                    column;

            }

            .info-grid {

                grid-template-columns: 1fr;

            }

            .button {

                text-align: center;

            }

        }

    </style>

</head>


<body>


<!-- =====================================================
     HEADER
====================================================== -->

<header>

    <h1>
        DashTech Computers
    </h1>

    <p>
        Administrator Panel
    </p>

</header>


<!-- =====================================================
     NAVIGATION
====================================================== -->

<nav>

    <a href="dashboard.php">
        Dashboard
    </a>

    <a href="bookings.php">
        Bookings
    </a>

    <a href="logout.php">
        Logout
    </a>

</nav>


<!-- =====================================================
     MAIN
====================================================== -->

<main>


    <div class="top-bar">

        <div>

            <h2>
                Booking Details
            </h2>

            <p>
                <?php
                echo htmlspecialchars(
                    $booking['booking_reference']
                );
                ?>
            </p>

        </div>


        <div>

            <a
                href="bookings.php"
                class="button secondary"
            >
                ← Back to Bookings
            </a>

        </div>

    </div>

    <?php if (!empty($_GET['notice'])): ?>
        <p class="notice"><?php echo htmlspecialchars((string) $_GET['notice']); ?></p>
    <?php endif; ?>

    <section class="card">
        <h3>Booking actions</h3>
        <p>Change the booking status. The customer receives an email only when the status changes.</p>
        <form class="status-form" method="post" action="update-booking.php">
            <input type="hidden" name="csrf_token" value="<?php echo htmlspecialchars(csrfToken(), ENT_QUOTES, 'UTF-8'); ?>">
            <input type="hidden" name="id" value="<?php echo (int) $booking['id']; ?>">
            <label for="status">Status
                <select id="status" name="status">
                    <?php foreach (adminStatuses() as $allowedStatus): ?>
                        <option value="<?php echo htmlspecialchars($allowedStatus); ?>" <?php echo $booking['status'] === $allowedStatus ? 'selected' : ''; ?>><?php echo htmlspecialchars($allowedStatus); ?></option>
                    <?php endforeach; ?>
                </select>
            </label>
            <button type="submit" class="button">Save status &amp; notify customer</button>
        </form>
    </section>


    <!-- =================================================
         BOOKING INFORMATION
    ================================================== -->

    <section class="card">

        <h3>
            Booking Information
        </h3>


        <div class="info-grid">


            <div class="info-item">

                <span class="info-label">
                    Booking Reference
                </span>

                <div class="info-value">

                    <?php
                    echo htmlspecialchars(
                        $booking[
                            'booking_reference'
                        ]
                    );
                    ?>

                </div>

            </div>


            <div class="info-item">

                <span class="info-label">
                    Status
                </span>

                <div class="info-value">

                    <span
                        class="status <?php
                            echo htmlspecialchars(
                                $statusClass
                            );
                        ?>"
                    >

                        <?php
                        echo htmlspecialchars(
                            $booking['status']
                        );
                        ?>

                    </span>

                </div>

            </div>


            <div class="info-item">

                <span class="info-label">
                    Date Submitted
                </span>

                <div class="info-value">

                    <?php
                    echo htmlspecialchars(
                        $booking['created_at']
                    );
                    ?>

                </div>

            </div>


            <div class="info-item">

                <span class="info-label">
                    Website Type
                </span>

                <div class="info-value">

                    <?php
                    echo htmlspecialchars(
                        $booking['website_type']
                    );
                    ?>

                </div>

            </div>


        </div>

    </section>


    <!-- =================================================
         CUSTOMER INFORMATION
    ================================================== -->

    <section class="card">

        <h3>
            Customer Information
        </h3>


        <div class="info-grid">


            <div class="info-item">

                <span class="info-label">
                    Full Name
                </span>

                <div class="info-value">

                    <?php
                    echo htmlspecialchars(
                        $booking['fullname']
                        );
                    ?>

                </div>

            </div>


            <div class="info-item">

                <span class="info-label">
                    Email Address
                </span>

                <div class="info-value">

                    <a
                        href="mailto:<?php
                            echo htmlspecialchars(
                                $booking['email']
                            );
                        ?>"
                    >

                        <?php
                        echo htmlspecialchars(
                            $booking['email']
                        );
                        ?>

                    </a>

                </div>

            </div>


            <div class="info-item">

                <span class="info-label">
                    Phone
                </span>

                <div class="info-value">

                    <a
                        href="tel:<?php
                            echo htmlspecialchars(
                                $booking['phone']
                            );
                        ?>"
                    >

                        <?php
                        echo htmlspecialchars(
                            $booking['phone']
                        );
                        ?>

                    </a>

                </div>

            </div>


            <div class="info-item">

                <span class="info-label">
                    Company
                </span>

                <div class="info-value">

                    <?php
                    echo htmlspecialchars(
                        $booking['company']
                        ?: 'Not provided'
                    );
                    ?>

                </div>

            </div>


        </div>

    </section>


    <!-- =================================================
         PROJECT DETAILS
    ================================================== -->

    <section class="card">

        <h3>
            Project Details
        </h3>


        <div class="project-details">

            <?php

            echo nl2br(
                htmlspecialchars(
                    $booking['project_details']
                    ?: 'No project details provided.'
                )
            );

            ?>

        </div>

    </section>


    <!-- =================================================
         ATTACHMENT
    ================================================== -->

    <section class="card">

        <h3>
            Attachment
        </h3>


        <div class="attachment">

            <?php if (
                !empty(
                    $booking['attachment']
                )
            ): ?>

                <p>
                    An attachment was submitted
                    with this booking.
                </p>

                <a
                    href="download.php?id=<?php echo (int) $booking['id']; ?>"
                    class="button"
                >
                    Download Attachment
                </a>

            <?php else: ?>

                <p>
                    No attachment was submitted.
                </p>

            <?php endif; ?>

        </div>

    </section>


</main>


<!-- =====================================================
     FOOTER
====================================================== -->

<footer
    style="
        text-align:center;
        padding:30px;
        color:#777;
    "
>

    &copy;
    <?php echo date('Y'); ?>
    DashTech Computers.
    All Rights Reserved.

</footer>


</body>

</html>
