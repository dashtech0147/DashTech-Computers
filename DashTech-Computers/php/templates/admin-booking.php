<?php ob_start(); ?>

<!DOCTYPE html>

<html>

<head>

<meta charset="UTF-8">

<title>New Booking</title>

</head>

<body style="margin:0;background:#f4f7fb;font-family:Arial,sans-serif;">

<table width="100%" cellpadding="0" cellspacing="0">

<tr>

<td align="center">

<table width="700" cellpadding="0" cellspacing="0" style="background:#ffffff;border-radius:12px;overflow:hidden;">

<tr>

<td style="background:#003366;padding:35px;text-align:center;">

<img src="https://www.dashtechwebhosting.com.ng/images/logo.png"

width="110"

alt="DashTech">

<h1 style="color:white;margin-top:15px;">

New Website Booking

</h1>

</td>

</tr>

<tr>

<td style="padding:35px;">

<p>

A new customer has submitted a booking.

</p>

<table width="100%" cellpadding="12">

<tr>

<td><strong>Booking Ref</strong></td>

<td><?= htmlspecialchars($bookingReference) ?></td>

</tr>

<tr>

<td><strong>Name</strong></td>

<td><?= htmlspecialchars($fullname) ?></td>

</tr>

<tr>

<td><strong>Email</strong></td>

<td><?= htmlspecialchars($email) ?></td>

</tr>

<tr>

<td><strong>Phone</strong></td>

<td><?= htmlspecialchars($phone) ?></td>

</tr>

<tr>

<td><strong>Company</strong></td>

<td><?= htmlspecialchars($company) ?></td>

</tr>

<tr>

<td><strong>Website</strong></td>

<td><?= htmlspecialchars($website) ?></td>

</tr>

<tr>

<td><strong>Project Details</strong></td>

<td><?= nl2br(htmlspecialchars($message)) ?></td>

</tr>

</table>

</td>

</tr>

<tr>

<td style="background:#003366;color:white;padding:25px;text-align:center;">

DashTech Computers

<br>

info@dashtechwebhosting.com.ng

<br>

+2347044390179

</td>

</tr>

</table>

</td>

</tr>

</table>

</body>

</html>

<?php return ob_get_clean(); ?>