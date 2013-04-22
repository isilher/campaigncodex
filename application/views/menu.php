    <div class="navbar navbar-inverse navbar-fixed-top">
      <div class="navbar-inner">
        <div class="container">
          <button type="button" class="btn btn-navbar" data-toggle="collapse" data-target=".nav-collapse">
            <span class="icon-bar"></span>
            <span class="icon-bar"></span>
            <span class="icon-bar"></span>
          </button>
          <a class="brand" href="{base_url()}">Campaigncodex</a>
          <div class="nav-collapse collapse">
            <ul class="nav">
              <!--<li class="active"><a href="#">Home</a></li> -->
              <li><a href="{site_url('home')}">Home</a></li>
              <li><a href="{site_url('forum')}">Forum</a></li>
<!--               <li><a href="{site_url('character')}">Characters</a></li> -->
<!--               <li><a href="{site_url('campaign')}">Campaigns</a></li> -->
<!--               <li><a href="{site_url('test')}">Tests</a></li> -->
              <!-- <li class="dropdown">
                <a href="#" class="dropdown-toggle" data-toggle="dropdown">Dropdown <b class="caret"></b></a>
                <ul class="dropdown-menu">
                  <li><a href="#">Action</a></li>
                  <li><a href="#">Another action</a></li>
                  <li><a href="#">Something else here</a></li>
                  <li class="divider"></li>
                  <li class="nav-header">Nav header</li>
                  <li><a href="#">Separated link</a></li>
                  <li><a href="#">One more separated link</a></li>
                </ul>
              </li> -->
            </ul>
            <ul class="nav pull-right">
				<li><a href="{site_url('auth/login')}">Login</a></li>
				<li><a href="{site_url('auth/register')}">Register</a></li>
			</ul>                 
          </div><!--/.nav-collapse -->
        </div>
      </div>
    </div>