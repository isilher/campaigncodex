{if $message != ''}
<div class="row">
	<div class="span7">
	 	<div class="alert {$message_type}"><button class="close" data-dismiss="alert" type="button">x</button><strong class="text">Info!</strong><br>{$message}</div>
	 </div>
</div>
{/if}

<div class="row">
	<div class="span7">
		<h2>{$title}</h2>
		{form_open(current_url(), $classes_form)}
			<div class="control-group{if form_error('username')} error{/if}">
				<label class="control-label" for="type">Username *</label>
				<div class="controls">
					<input type="text" name="username" id="value" placeholder="Enter username" value="{set_value('username')}">
				</div>
			</div>		
			<div class="control-group{if form_error('password')} error{/if}">
				<label class="control-label" for="type">Password *</label>
				<div class="controls">
					<input type="password" name="password" id="password" placeholder="Enter password">
				</div>
			</div>		
			<div class="control-group">
				<div class="controls">
					<button type="submit" class="btn btn-success">Login</button>
				</div>
			</div>				
		{form_close()}			
	</div>
</div>