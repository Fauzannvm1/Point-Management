<h1>Teacher Dashboard</h1>
<p>Welcome, {{ auth()->user()->name }}</p>

<ul>
    <li>Give Points</li>
    <li>View Student List</li>
</ul>
<br>
<a href="{{ route ('logout') }}">Log Out</a>