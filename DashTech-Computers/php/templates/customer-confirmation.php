<?php ob_start(); ?>

<!DOCTYPE html>

<html>

<head>

<meta charset="UTF-8">

</head>

<body style="margin:0;background:#eef3f8;font-family:Arial,sans-serif;">

<table width="100%">

<tr>

<td align="center">

<table width="700" style="background:#ffffff;border-radius:12px;overflow:hidden;">

<tr>

<td style="background:#003366;padding:40px;text-align:center;">

<img src="https://www.dashtechwebhosting.com.ng/images/logo.png"

width="110">

<h1 style="color:white;">

Thank You!

</h1>

</td>

</tr>

<tr>

<td style="padding:40px;">

<h2>

Hello <?= htmlspecialchars($fullname) ?>,

</h2>

<p>

Thank you for choosing DashTech Computers.

</p>

<p>

We have successfully received your website booking.

</p>

<table width="100%" cellpadding="12" style="background:#f7f9fc;border-radius:8px;">

<tr>

<td>

<strong>Booking Reference</strong>

</td>

<td>

<?= htmlspecialchars($bookingReference) ?>

</td>

</tr>

<tr>

<td>

<strong>Website Type</strong>

</td>

<td>

<?= htmlspecialchars($website) ?>

</td>

</tr>

<tr>

<td>

<strong>Estimated Delivery</strong>

</td>

<td>

5 Working Days

</td>

</tr>

</table>

<h3>

Next Steps

</h3>

<ul>

<li>Our team will review your project.</li>

<li>You may be contacted for additional information.</li>

<li>Development begins after confirmation.</li>

<li>You'll receive progress updates throughout the project.</li>

</ul>

<p align="center">

<a href="https://wa.me/2347044390179"

style="background:#25D366;color:white;padding:15px 35px;text-decoration:none;border-radius:6px;">

Chat on WhatsApp

</a>

</p>

</td>

</tr>

<tr>

<td style="background:#003366;color:white;padding:30px;text-align:center;">

<strong>DashTech Computers</strong>

<br>

ICT & Consultancy Services

<br><br>

Email:

info@dashtechwebhosting.com.ng

<br>

Phone:

+2347044390179

<br><br>

© <?= date("Y") ?>

DashTech Computers

</td>

</tr>

</table>

</td>

</tr>

</table>

</body>

</html>

<?php return ob_get_clean(); ?>