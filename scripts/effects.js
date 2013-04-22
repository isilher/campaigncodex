(function( $ ) {

	// initiate
	initeffects();
	
	// create effects
	var effectlist = new EffectList();	
	
	// add button
	$("#effect_add").click(function() {
		// grab the values
		var effect = $('#effect_name').val();
		var target = $('#effect_target').val();
		
		// add effect to effectlist
		effectlist.addEffect( effect, target );
	});	
	
	/*
	 * Init function
	 */
	function initeffects()
	{
		// reset the input fields
		$("#effect_name").val('');
		$("#effect_target").val('All PCs');
		
		effect_welcometext();
	}
	
	/*
	 * Set the welcome text
	 */
	function effect_welcometext()
	{
		var text = "<div id='effects_empty'><br><br><p>No effects yet</p><p>Please add some by using the 'add effect' option</p><br><p>Examples: Bless, haste or enlarge</p></div>";
		$("#effects_track").html( text );
		$("#effects_empty").hide().fadeIn('slow');
	}	
	
	// effectlist object
	function EffectList()
	{
		// array of effect objects
		var effects = new Array();
		
		var lasteffect_id = 0;
		
		/*
		 * Create a effect object and add it to the effects array
		 */
		this.addEffect = function( effect, target )
		{
			// create the new effect and add it
			var new_effect = new Effect( lasteffect_id, effect, target );
			effects.push( new_effect );
			
			// up lasteffect_id
			lasteffect_id++;
			
			// reset the input fields
			$("#effect_name").val('');
			$("#effect_target").val('All PCs');
			
			// draw the effects list
			this.drawEffects();
			
			// focus back on effect
			$("#effect_name").focus();
		}
		
		/*
		 * Remove a effect object from the effects array
		 */
		this.removeEffect = function( id )
		{
			//TODO
		}
		
		this.drawEffects = function ( )
		{
			if( effects.length < 1 ) {
				// no effects, show welcometext
				effect_welcometext();
				
				return;
			}
			
			// remove exsisting track
			$("#effects_track").html( 'Second!' );
			
			// grab template and compile
			var source   = $("#effect-template").html();
			var template = Handlebars.compile( source );
			var html = '';
			
			// create html for each effect
			for (var i=0; i < effects.length; i++) 
			{
				var value = effects[i];
				
				var context = { 
						effect: value.effect,
						target: value.target, 
						id: value.id,
						};
				html = html + template( context );
			}
			
			// display the effects list
			$("#effects_track").html( html );			
			
		}
	}
	
	// effect object
	function Effect( id, effect, target )
	{
		// assign parameters
		this.id = id;
		this.effect = effect;
		this.target = target;
	}
	
})( jQuery );