<?php

// =====================================================
// DashTech Computers - Booking Management
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
// FILTER
// =====================================================

$status = trim($_GET['status'] ?? '');

$allowedStatuses = adminStatuses();


// Only accept valid status filters

if (
    $status !== '' &&
    !in_array($status, $allowedStatuses, true)
) {

    $status = '';

}


// =====================================================
// SEARCH
// =====================================================

$search = trim($_GET['search'] ?? '');


// =====================================================
// LOAD BOOKINGS
// =====================================================

try {

    $sql = "
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
    ";

    $conditions = [];

    $parameters = [];


    // Status filter

    if ($status !== '') {

        $conditions[] = "status = :status";

        $parameters[':status'] = $status;

    }


    // Search filter

    if ($search !== '') {

        $conditions[] = "
            (
                booking_reference LIKE :search
                OR fullname LIKE :search
                OR email LIKE :search
                OR phone LIKE :search
                OR company LIKE :search
                OR website_type LIKE :search
            )
        ";

        $parameters[':search'] = '%' . $search . '%';

    }


    // Add conditions

    if (!empty($conditions)) {

        $sql .= ' WHERE ' .
            implode(' AND ', $conditions);

    }


    // Newest bookings first

    $sql .= "
        ORDER BY created_at DESC
    ";


    $stmt = $pdo->prepare($sql);

    $stmt->execute($parameters);

    $bookings = $stmt->fetchAll();


} catch (PDOException $e) {

    error_log(
        'DashTech Bookings Error: ' .
        $e->getMessage()
    );

    die(
        'Unable to load bookings.'
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
        Bookings | DashTech Computers
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
        ================================================= */

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
        ================================================= */

        nav {

            background: #ffffff;

            padding: 15px 20px;

            border-bottom:
                1px solid #ddd;

        }

        nav a {

            text-decoration: none;

            color: #003366;
            font-weight: 600;

            margin-right: 20px;

        }

        nav a:hover {

            text-decoration: underline;

        }


        /* =================================================
           MAIN
        ================================================= */

        main {

            max-width: 1400px;

            margin: 30px auto;

            padding: 0 20px;

        }


        /* =================================================
           PAGE TITLE
        ================================================= */

        .page-header {

            display: flex;

            justify-content:
                space-between;

            align-items: center;

            gap: 20px;

            margin-bottom: 25px;

        }

        .page-header h2 {

            margin: 0;

        }


        /* =================================================
           FILTER AREA
        ================================================= */

        .filters {

            background: #ffffff;

            padding: 20px;

            border-radius: 10px;

            margin-bottom: 25px;

            box-shadow:
                0 2px 8px
                rgba(0,0,0,.06);

        }

        .filters form {

            display: flex;

            flex-wrap: wrap;

            gap: 10px;

        }

        .filters input,
        .filters select {

            padding: 11px 12px;

            border:
                1px solid #ccc;

            border-radius: 6px;

            font-size: 14px;

        }

        .filters input {

            min-width: 250px;

        }


        .button {

            display: inline-block;

            padding: 11px 18px;

            border-radius: 6px;

            border: none;

            background: #003366;

            color: #ffffff;

            text-decoration: none;

            cursor: pointer;

        }

        .button:hover {

            opacity: .9;

        }

        .button.secondary {

            background: #666;

        }


        /* =================================================
           TABLE
        ================================================= */

        .table-container {

            background: #ffffff;

            border-radius: 10px;

            overflow-x: auto;

            box-shadow:
                0 2px 8px
                rgba(0,0,0,.06);

        }

        table {

            width: 100%;

            border-collapse:
                collapse;

            min-width: 950px;

        }

        th,
        td {

            padding: 14px;

            text-align: left;

            border-bottom:
                1px solid #eee;

        }

        th {

            background: #003366;

            color: #ffffff;

            white-space: nowrap;

        }

        tr:hover td {

            background: #f8fafc;

        }


        /* =================================================
           STATUS
        ================================================= */

        .status {

            display: inline-block;

            padding: 5px 10px;

            border-radius: 20px;

            font-size: 12px;

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
           EMPTY STATE
        ================================================= */

        .empty {

            padding: 50px;

            text-align: center;

        }


        /* =================================================
           MOBILE
           ================================================= */

        @media (
            max-width: 700px
        ) {

            main {

                padding: 0 12px;

                margin-top: 20px;

            }

            .page-header {

                align-items:
                    flex-start;

                flex-direction:
                    column;

            }

            .filters input {

                width: 100%;

                min-width: 0;

            }

            .filters select {

                width: 100%;

            }

            .button {

                width: 100%;

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
        Booking Management
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
     MAIN CONTENT
====================================================== -->

<main>


    <div class="page-header">

        <div>

            <h2>
                Website Bookings
            </h2>

            <p>
                View and manage customer website
                hosting requests.
            </p>

        </div>

    </div>


    <!-- =================================================
         FILTERS
    ================================================== -->

    <section class="filters">

        <form
            method="GET"
            action=""
        >


            <input
                type="search"
                name="search"
                placeholder="Search bookings..."
                value="<?php
                    echo htmlspecialchars($search);
                ?>"
            >


            <select name="status">

                <option value="">
                    All Statuses
                </option>

                <?php foreach (
                    $allowedStatuses
                    as $allowedStatus
                ): ?>

                    <option
                        value="<?php
                            echo htmlspecialchars(
                                $allowedStatus
                            );
                        ?>"
                        <?php
                        echo (
                            $status ===
                            $allowedStatus
                        )
                        ? 'selected'
                        : '';
                        ?>
                    >

                        <?php
                        echo htmlspecialchars(
                            $allowedStatus
                        );
                        ?>

                    </option>

                <?php endforeach; ?>

            </select>


            <button
                type="submit"
                class="button"
            >
                Search
            </button>


            <a
                href="bookings.php"
                class="button secondary"
            >
                Clear
            </a>


        </form>

    </section>


    <!-- =================================================
         BOOKINGS TABLE
    ================================================== -->

    <section class="table-container">

        <?php if (empty($bookings)): ?>

            <div class="empty">

                <h3>
                    No bookings found
                </h3>

                <p>
                    There are no bookings matching
                    your search criteria.
                </p>

            </div>

        <?php else: ?>


            <table>

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
                            Phone
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
                    $bookings
                    as $booking
                ): ?>


                    <?php

                    $statusClass =
                        'status-' .
                        strtolower(
                            $booking['status']
                        );

                    ?>


                    <tr>


                        <td>

                            <strong>

                                <?php
                                echo htmlspecialchars(
                                    $booking[
                                        'booking_reference'
                                    ]
                                );
                                ?>

                            </strong>

                        </td>


                        <td>

                            <?php
                            echo htmlspecialchars(
                                $booking['fullname']
                            );
                            ?>

                            <?php
                            if (
                                !empty(
                                    $booking['company']
                                )
                            ):
                            ?>

                                <br>

                                <small>

                                    <?php
                                    echo htmlspecialchars(
                                        $booking['company']
                                    );
                                    ?>

                                </small>

                            <?php endif; ?>

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
                                $booking['phone']
                            );
                            ?>

                        </td>


                        <td>

                            <?php
                            echo htmlspecialchars(
                                $booking[
                                    'website_type'
                                ]
                            );
                            ?>

                        </td>


                        <td>

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
                                href="view-booking.php?id=<?php
                                    echo (int)
                                        $booking['id'];
                                ?>"
                                class="button"
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
