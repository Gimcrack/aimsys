<nav class="navbar navbar-default navbar-aimsys">
  <div class="container-fluid">
    <div class="navbar-header">
      <button type="button" class="navbar-toggle collapsed" data-toggle="collapse" data-target="#bs-example-navbar-collapse-1">
        <span class="sr-only">Toggle Navigation</span>
        <span class="icon-bar"></span>
        <span class="icon-bar"></span>
        <span class="icon-bar"></span>
      </button>

      <img class="navbar-brand-logo" height="50" width="231" src="{{ asset('images/logo-sans-small.png')}}" alt="Small Aimsys Logo" />
      <a class="navbar-brand hidden-xs" href="#"> Aircraft Inventory &amp; Maintenance System</a>
    </div>

    <div class="collapse navbar-collapse" id="bs-example-navbar-collapse-1">
      <ul class="nav navbar-nav">
        <li><a href="{{ url('/') }}">Home</a></li>
      </ul>

      <ul class="nav navbar-nav navbar-right">
        @if (Auth::guest())
          <li><a href="{{ url('/auth/login') }}">Login</a></li>
          <li><a href="{{ url('/auth/register') }}">Register</a></li>
        @else
          <li class="dropdown">
            <a href="#" class="dropdown-toggle" data-toggle="dropdown" role="button" aria-expanded="false">{{ Auth::user()->name }} <span class="caret"></span></a>
            <ul class="dropdown-menu" role="menu">
              @if( Auth::user()->isAdmin() )
              <li class="menu-section-heading"> Admin Menu </li>
              <li>
                <a href="{{ action('Admin\PagesController@index')}}"> <i class="fa fa-fw fa-gear"></i> Admin Home</a>
              </li>
              <li>
                <a href="{{ action('Admin\UserController@index')}}"> <i class="fa fa-fw fa-user"></i> Manage Users</a>
              </li>
              <li>
                <a href="{{ action('Admin\GroupController@index')}}"> <i class="fa fa-fw fa-users"></i> Manage Groups</a>
              </li>
              <li class="divider"></li>
              @endif


              @if( Auth::user()->isSuperAdmin())
                <li class="menu-section-heading"> Super Admin Menu </li>
                <li>
                  <a href="{{ action('Admin\ColParamController@index')}}"><i class="fa fa-fw fa-database"></i> Manage ColParams</a>
                </li>
                <li class="divider"></li>
              @endif

              <li><a href="{{ url('/auth/logout') }}"> <i class="fa fa-fw fa-close"></i> Logout</a></li>
            </ul>
          </li>
        @endif
      </ul>
    </div>
  </div>
</nav>
