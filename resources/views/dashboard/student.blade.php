<h1>Student Dashboard</h1>
<p>Welcome, {{ auth()->user()->name }}</p>

<ul>
    <li>View My Points</li>
    <li>View Tasks</li>
</ul>
<br>
<a href="{{ route ('login') }}">Log Out</a>