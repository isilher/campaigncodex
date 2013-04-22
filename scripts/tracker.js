(function( $ ) {
	// initiate
	inittracker();
	
	// create tracker
	var tracker = new InitTracker();
	
	// add button
	$("#add").click(function() {
		// grab the values
		var name = $('#new').val();
		var init = $('#init_input').val();
		var type = $('input[name=type]:checked').val();
		
		// create new initiative object
		tracker.addInitiative(name, init, type);
		tracker.drawInitiative();
	});
	
	// start button
	$("#start_button").click(function() {
		// set the tracker combat state
		tracker.startCombat();
	});
	
	// next button
	$("#next").click( function() {
		tracker.next();
	});

	// previous button
	$("#prev").click( function() {
		tracker.previous();
	});	

	// delay button
	$("#delay").click( function() {
		tracker.delay();
	});
	
	// ready button
	$("#ready").click( function() {
		tracker.ready();
	});
	
	// add name1 handlers
	$("#decide_parent").on("click", "#name1", function( event ) {
		var id = $(this).attr('data-id');
		tracker.decided( id, 'name1' );
	});	
	
	// add name2 handlers
	$("#decide_parent").on("click", "#name2", function( event ) {
		var id = $(this).attr('data-id');
		tracker.decided( id, 'name2' );
	});	
	
	// add delete handlers
	$("#track").on("click", ".init_container .delete", function( event ) {
		var id = $(this).parent().attr('data-id');
		tracker.removeInitiative( id );		
	});
	
	// add delay button handlers
	$("#track").on("click", ".init_container .delay_button", function( event ) {		
		var id = $(this).parent().attr('data-id');
		tracker.useDelay( id );
	});		
	
	// add ready button handlers
	$("#track").on("click", ".init_container .ready_button", function( event ) {
		var id = $(this).parent().attr('data-id');
		tracker.useReady( id );
	});
	
	/*
	 * Set the welcome text
	 */
	function welcometext()
	{
		var text = "<div id='track_empty'><br><br><p>No players or monsters yet</p><p>Please add some by using the 'add' option</p><br><p>May the dice be with you</p></div>";
		$("#track").html( text );
		$("#track_empty").hide().fadeIn('slow');
	}
	
	/*
	 * Init function
	 */
	function inittracker()
	{
		// reset the in combat buttons to disabled
		$("#prev").attr('disabled', '');
		$("#next").attr('disabled', '');
		$("#delay").attr('disabled', '');
		$("#ready").attr('disabled', '');
		
		// reset the 'init_input' field back to writeable
		$("#init_input").removeAttr('disabled');
		$("#init_input").val('');
		
		// reset the 'new' field
		$("#new").val('');
		
		// set the welcome text
		welcometext();
	}	
	
	// inittracker object
	function InitTracker()
	{
		// array of initiative objects
		var initiatives = new Array();
		
		// boolean to determine combat state
		var combat = false;
		
		var last_id = 0;
		
		/*
		 * Create a initiative object and add it to the initiatives array
		 */
		this.addInitiative = function(name, init, type, position, id)
		{
			if( init== '' ) {
				// if empty init, then do not add
				return;
			}
			
			if( name== '' ) {
				// if empty name, then do not add
				return;
			}
			
			if( combat == true ) {
				if ( initiatives.length > 0 )
				{
					// grab the current initiative
					init = initiatives[0].init;
					
					// increase the rest of the initiative by one
					for (var i=0; i < initiatives.length; i++) 
					{
						var value = initiatives[i];
						value.position = value.position + 1;
					}
				} 
				else 
				{
					init = 0;
				}
			}
			
			// create the new initiative and add it
			var init = new Initiative(name, init, type, 0, last_id + 1);
			
			// up the last_id
			last_id++;
			
			// see if there is anyone already in initiative with the same init
			if ( combat == false )
			{
				for (var i=0; i < initiatives.length; i++) 
				{
					var value = initiatives[i];
					if( value.init == init.init ) {
						// we got the same init, decide box
						this.decide( init, value );
					}
				}
			}
			
			// push it to the initiatives array
			initiatives.push( init );
			
			// reset the 'new' and 'init_input' fields
			if ( combat == false )
			{
				$("#init_input").val('');
			}
			
			$("#new").val('');
			$("#new").focus();
		}
		
		/*
		 * Remove a initiative object from the initiatives array
		 */
		this.removeInitiative = function( id )
		{
			// find the initiative to delete
			for (var i=0; i < initiatives.length; i++) 
			{
				var value = initiatives[i];
				if( value.id == id ) {
					// this is the one! Take him down!
					initiatives.splice( i, 1 );
					this.drawInitiative();
					
					// exit the loop
					return;
				}
			}			
		}
		
		/*
		 * Sort the initiative order
		 */
		this.sortInitiative = function()
		{
			if( combat == false ) {
				initiatives.sort( sortOutCombat );
			} else {
				initiatives.sort( sortInCombat );
			}
		}
		
		/*
		 * Sorts the initiative order out of combat
		 */
		sortOutCombat = function(a, b)
		{
			return b.init - a.init;
		}
		
		/*
		 * Sorts the initiative order in combat
		 */
		sortInCombat = function(a, b)
		{
			return a.position - b.position;
		}		
		
		/*
		 * Draw the initiative order
		 */
		this.drawInitiative = function()
		{
			if( initiatives.length < 1 ) {
				// no initiatives, show welcometext
				welcometext();
				return;
			}
			
			this.sortInitiative();
			
			// remove exsisting track
			$("#track").html( 'Second!' );
			
			// grab template and compile
			var source   = $("#player-template").html();
			var template = Handlebars.compile( source );
			var html = '';
			
			// create html for each initiative
			for (var i=0; i < initiatives.length; i++) 
			{
				var value = initiatives[i];
				
				console.log('position: ' + value.position);
				
				var context = { 
						name: value.name, 
						init_input: value.init, 
						type: value.type,
						id: value.id,
						delay: value.delay,
						ready: value.ready
						};
				html = html + template( context );
			}
			console.log('end------');
			
			// display the initiative order
			$("#track").html( html );
		}
		
		/*
		 * Start combat
		 */
		this.startCombat = function()
		{
			// set tracker combat state
			combat = true;
			
			// activate the in combat buttons
			$("#prev").removeAttr('disabled');
			$("#next").removeAttr('disabled');
			$("#delay").removeAttr('disabled');
			$("#ready").removeAttr('disabled');			
			
			// deactive the 'type' field
			$("#init_input").attr('disabled', '');
			$("#init_input").val('in combat');
		
			// remove the start div
			$("#start_container").remove();
			
			// initialize the initiative order
			for (var i=0; i < initiatives.length; i++) 
			{
				var value = initiatives[i];
				value.position = i + 1;
			}
			
			$("#new").focus();
		}
		
		/*
		 * Move initiative forward
		 */
		this.next = function()
		{
//			for (var i=0; i < initiatives.length; i++) 
//			{
//				var value = initiatives[i];
//				
//				
//				
//				if( i == 0 ) {
//					// remove the first initiative and return it
//					initiatives.shift();
//				} else {
//					// all the other initiatives
//					value.position = value.position - 1;
//				}			
//			}
			
			var first = initiatives.shift();
			
			console.log(initiatives.push( first ));
			
			
			// re-initialize the initiative order
			for (var i=0; i < initiatives.length; i++) 
			{
				var value = initiatives[i];
				value.position = i + 1;
			}	
			
			this.drawInitiative();
		}
		
		/*
		 * Move initiative backward
		 */
		this.previous = function()
		{
			var last = initiatives.pop();
			
			initiatives.unshift( last );
			
			// re-initialize the initiative order
			for (var i=0; i < initiatives.length; i++) 
			{
				var value = initiatives[i];
				value.position = i + 1;
			}	
			
			this.drawInitiative();			
			
//			for (var i=0; i < initiatives.length; i++) 
//			{
//				var value = initiatives[i];
//				
//				if( i == (initiatives.length - 1) ) {
//					// only the last initiative
//					value.position = 0;
//				} else {
//					// all the other initiatives
//					value.position = value.position + 1;
//				}
//			}
//			
//			this.drawInitiative();
		}
		
		/*
		 * Add delay action to initiative
		 */
		this.delay = function()
		{
			initiatives[0].delay = true;	
			this.next();
		}
		
		/*
		 * Add ready action to initiative
		 */
		this.ready = function()
		{
			initiatives[0].ready = true;
			this.next();
		}

		/*
		 * Use delay action
		 */
		this.useDelay = function( id )
		{
			var delayer_position = 0;
			
			// grab the delayer
			for (var i=0; i < initiatives.length; i++) 
			{
				var value = initiatives[i];
				
				if( value.id == id ) 
				{
					// found the delayer
					delayer_position = value.position;
					
					// set the delayer position
					value.position = 1;
					value.delay = false;
					
					// set new initiative for the initiative
					value.init = initiatives[0].init;
				}
			}
			
			// change the positions of the rest
			for (var i=0; i < initiatives.length; i++) 
			{
				var value = initiatives[i];
				
				if (value.id == id )
				{
					// do nothing
				}
				else if( value.position > delayer_position ) 
				{
					//value.position = value.position - 1;
				} 
				else if( value.position < delayer_position )
				{
					value.position = value.position + 1;
				}
			}
			
			this.drawInitiative();			
		}
		
		/*
		 * Use ready action
		 */
		this.useReady = function( id )
		{
			var readier_position = 0;
			
			// grab the readier
			for (var i=0; i < initiatives.length; i++) 
			{
				var value = initiatives[i];
				
				if( value.id == id ) 
				{
					// found the delayer
					readier_position = value.position;
					
					// set the readier position
					value.position = 1;
					value.ready = false;
					
					// set new initiative for the initiative
					value.init = initiatives[0].init;					
				}
			}
			
			// change the positions of the rest
			for (var i=0; i < initiatives.length; i++) 
			{
				var value = initiatives[i];
				
				if (value.id == id )
				{
					// do nothing
				}
				else if( value.position < readier_position )
				{
					
					value.position = value.position + 1;
				}
			}
			
			this.drawInitiative();			
		}
		
		/*
		 * Decide who goes first
		 */
		this.decide = function( i_new, i_old )
		{
			// grab template and compile
			var source   = $("#decide-template").html();
			var template = Handlebars.compile( source );
			var html = '';
			
			var context = { 
					name1: i_new.name,
					name1_id: i_new.id,
					name2: i_old.name,
					name2_id: i_old.id
					};
			
			html = html + template( context );
			
			// display the decide box
			$("#decide_parent").html( html );			
		}
		
		/*
		 * Handle the clicking of the decide buttons
		 */
		this.decided = function( id, button )
		{
			var initiative;
			
			// find the initiative
			for (var i=0; i < initiatives.length; i++) 
			{
				var value = initiatives[i];
				if( value.id == id )
				{
					// found it
					initiative = value;
				}
			}

			var exsisting;
			var location = 0;
			
			// find the initiative that is the same
			for (var i=0; i < initiatives.length; i++) 
			{
				var value = initiatives[i];
				if( value.init == initiative.init )
				{
					// got it!
					exsisting = value;
					location = i;
				}
					
			}
			
			if( button == 'name1' )
			{
				// remove the exsisting initiative
				initiatives.splice( location, 1);			
				
				// add it to the initiatives 
				initiatives.splice( location -1 , 0, initiative );
			}
			
			this.drawInitiative();
			
			$("#decide_parent").html("");
		}
		
		/*
		 * Print the entire initiatives to a string
		 */
		this.toString = function()	
		{
			initiatives.sort( this.sortInitiative );
			var output = '';
			
			for (var i=0; i < initiatives.length; i++) 
			{
				var value = initiatives[i];
				output = output + '<br>' + value.name + ' init: ' + value.init;
			}
			
			return output;
		}
	}
	
	// initiative object
	function Initiative( name, init, type, position, id )
	{
		// assign parameters
		this.name = name;
		this.init = parseInt(init);
		this.type = type;
		this.position = position;
		this.id = id;
		
		this.delay = false;
		this.ready = false;
	}
	
})( jQuery );