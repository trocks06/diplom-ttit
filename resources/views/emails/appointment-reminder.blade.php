<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Напоминание о приёме</title>
</head>
<body>
<h2>Здравствуйте, {{ $patientName }}!</h2>
<p>Напоминаем, что завтра <strong>{{ $startTime }}</strong> у вас назначен приём у {{ $doctorName }}.</p>
<p>Если у вас изменились планы, пожалуйста, свяжитесь с клиникой заблаговременно.</p>
<p>С уважением,<br>Стоматологическая клиника</p>
</body>
</html>
