<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Parent - PointMS</title>
    <link rel="icon" type="image/png" href="{{ asset('storage_backup/Icon.png') }}">
</head>
<body>
<h1>Parent Dashboard</h1>
<p>Welcome, {{ auth()->user()->name }}</p>

<ul>
    <li>View Child Points</li>
    <p>bla</p>
</ul>
<br>
<a href="{{ route ('logout') }}">Log Out</a>
</body>
</html>
