<h1>Admin Dashboard</h1>
<p>Welcome, {{ auth()->user()->name }}</p>

<ul>
    <li>Manage Users</li>
    <li>Manage Points</li>
</ul>
<br>
<a href="{{ route ('login') }}">Log Out</a>