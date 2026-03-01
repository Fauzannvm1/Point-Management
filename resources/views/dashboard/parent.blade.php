<h1>Parent Dashboard</h1>
<p>Welcome, {{ auth()->user()->name }}</p>

<ul>
    <li>View Child Points</li>
</ul>
<br>
<a href="{{ route ('login') }}">Log Out</a>