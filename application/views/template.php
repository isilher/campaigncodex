<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="utf-8">
        <meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1">
        <title>{$title} - Campaigncodex</title>
        <meta name="description" content="">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">

        <link rel="icon" href="{base_url()}images/favicon.ico" type="image/x-icon" />
        {'css'|helper:'carabiner':'carabiner_display'}
    </head>
    <body>
	      	{include file="menu.php"}
	    <div class="container">
	    	<div class="row">
		    	<div id="searchbar" class="span4 offset8">
			    	<form class="navbar-form pull-right" method="post" action="{site_url('search')}">
		              	<input name="search" class="span3" type="text" placeholder="Search for characters">
		            	<button type="submit" class="btn">Search</button>
		            </form>
	            </div>
            </div>
        	{include file=$content}
        </div>

        <script src="//ajax.googleapis.com/ajax/libs/jquery/1.8.2/jquery.min.js"></script>
        <script>window.jQuery || document.write('<script src="{base_url()}scripts/jquery-1.8.2.min.js"><\/script>')</script>

        {'js'|helper:'carabiner':'carabiner_display'}
        
        <!-- Google Analytics: change UA-XXXXX-X to be your site's ID. -->
        {literal}
        <script>
            var _gaq=[['_setAccount','UA-XXXXX-X'],['_trackPageview'],['_gat._anonymizeIp']];
            (function(d,t){var g=d.createElement(t),s=d.getElementsByTagName(t)[0];
            g.src=('https:'==location.protocol?'//ssl':'//www')+'.google-analytics.com/ga.js';

            s.parentNode.insertBefore(g,s)}(document,'script'));
        </script>
        {/literal}
    </body>
</html>
