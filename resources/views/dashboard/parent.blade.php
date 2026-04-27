<h1>Parent Dashboard</h1>
<p>Welcome, {{ auth()->user()->name }}</p>

<ul>
    <li>View Child Points</li>
    <p>bla</p>
</ul>
<br>
<a href="{{ route ('logout') }}">Log Out</a>