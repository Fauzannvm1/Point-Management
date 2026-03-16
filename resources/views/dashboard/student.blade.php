<h1>Student Dashboard</h1>
<p>Welcome, {{ auth()->user()->name }}</p>

<ul>
    <li>View My Points</li>
    <li>View Tasks</li>
</ul>
<br>
<div class="px-3 pb-5">
    <a href="{{ route('logout') }}" onclick="event.preventDefault(); document.getElementById('logoutForm').submit();" class="nav-item hover:!bg-red-500/10 hover:!text-red-400">
      <svg fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"/></svg>
      Logout
    </a>
    <form id="logoutForm" action="{{ route('logout') }}" method="POST" class="hidden">@csrf</form>
  </div>