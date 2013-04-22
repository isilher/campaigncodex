
<!-- <!doctype html>-->
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN" "http://www.w3.org/TR/html4/loose.dtd">
<html>
	<head>
		<title>Combat tracker - Campaign Codex</title>
		<link rel="stylesheet" type="text/css" href="{base_url()}css/reset.css">
		<link rel="stylesheet" type="text/css" href="{base_url()}css/tracker.css">
		<link rel="stylesheet" type="text/css" href="{base_url()}css/effects.css">
		<script type="text/javascript" src="http://code.jquery.com/jquery-1.7.2.min.js"></script>
	</head>
	{literal}
	<body>
		<div id="main">
			<!--- START TRACKER --->
			<div id="tracker">
				<div id="init">
					<h2>Combat tracker</h2>
					<div id="todo">
						TODO
						<ul>
							<li>- Zelfde initiatief meer dan 2 tegelijk</li>
							<li>- Hit enter to submit</li>
							<li>- Effects deleten</li>
						</ul>
					</div>
					<div id="init_panel">
						
						Name: <input id="new" type="text"> Init: <input id="init_input" type="text">
						[ <input id="type" name="type" type="radio" value="player" checked="checked">Player ] [<input id="type" name="type" type="radio" value="monster">Monster ]
						<button id="add">Add</button><br>
						<button id="prev" disabled>Previous</button>
						<button id="next" disabled>Next</button>
						<button id="delay" disabled>Delay</button>
						<button id="ready" disabled>Ready</button>
					</div>
					<div class="clear"></div>
					<div id="start_container"><button id="start_button">Start</button></div>
					<div id="decide_parent"></div>
					<div id="track"></div>						
				</div><!-- end init -->
			</div><!-- end tracker -->
			<!--- END TRACKER --->
			<!--- START EFFECTS --->
			<div id="effects">
				<h2>Effects</h2>
				<div id="effects_panel">
					<label for="effect_name">Effect:</label> <input id="effect_name" type="text"><br>
					<label for="effect_Target">Target:</label> <input id="effect_target" type="text"><br>
					<center><button id="effect_add">Add effect</button></center>
				</div><!-- end effects_panel -->
				<div id="effects_track"></div>		
			</div><!-- end effects -->
			<!--- END EFFECTS --->
		</div><!-- end main -->
		<script id="player-template" type="text/x-handlebars-template">
  			<div class="init_container {{type}}" data-id="{{id}}">
				<span class="name">{{name}}</span>
				{{#if delay}}<span class="delay_button">Delaying</span>{{/if}}
				{{#if ready}}<span class="ready_button">Readied</span>{{/if}}
				<div class="delete">
					X
				</div>
				<div class="init_counter">
					{{init_input}}
				</div>
			</div><!-- end init_container -->
		</script>
		<script id="decide-template" type="text/x-handlebars-template">
  			<div class="decide_container">
				<p>{{name1}} and {{name2}} have the same init, who goes first?</p>
				<div id="decide_buttons">
					<button id="name1" data-id="{{name1_id}}">{{name1}}</button> 
					<button id="name2"  data-id="{{name2_id}}">{{name2}}</button>
				</div>
			</div><!-- end decide_container -->
		</script>
		<script id="effect-template" type="text/x-handlebars-template">
  			<div class="effect_container" data-id="{{id}}">
				<div class="effect_info">{{effect}} ({{target}})</div>
				<div class="delete">
					X
				</div>
			</div><!-- end init_container -->
		</script>
		{/literal}		
		<script type="text/javascript" src="{base_url()}scripts/handlebars.js"></script>
		<script type="text/javascript" src="{base_url()}scripts/tracker.js"></script>
		<script type="text/javascript" src="{base_url()}scripts/effects.js"></script>
	</body>
</html>