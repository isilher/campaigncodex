{if $message != ''}
<div class="row">
	<div class="span7">
	 	<div class="alert alert-error"><button class="close" data-dismiss="alert" type="button">x</button><strong class="text-error">Error!</strong><br>{$message}</div>
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
					<input type="text" name="username" id="value" placeholder="Enter desired username" value="{set_value('username')}">
				</div>
			</div>		
			<div class="control-group{if form_error('email')} error{/if}">
				<label class="control-label" for="email">E-mail address *</label>
				<div class="controls">
					<input type="text" name="email" id="value" placeholder="Enter e-mail address" value="{set_value('email')}">
				</div>
			</div>
			<div class="control-group{if form_error('confirmEmail')} error{/if}">
				<label class="control-label" for="confirmEmail">Confirm e-mail address *</label>
				<div class="controls">
					<input type="text" name="confirmEmail" id="confirmEmail" placeholder="Re-enter e-mail address" value="{set_value('confirmEmail')}">
				</div>
			</div>
			<div class="control-group{if form_error('password')} error{/if}">
				<label class="control-label" for="type">Password *</label>
				<div class="controls">
					<input type="password" name="password" id="password" placeholder="Enter password">
				</div>
			</div>
			<div class="control-group{if form_error('password')} error{/if}">
				<label class="control-label" for="type">Confirm password *</label>
				<div class="controls">
					<input type="password" name="confirmPassword" id="confirmPassword" placeholder="Re-enter password">
				</div>
			</div>				
<!-- 			<div class="control-group{if form_error('terms')} error{/if}"> -->
<!-- 				<label class="control-label" for="terms">Read and agreed to the <a href="">terms of use</a> *</label> -->
<!-- 				<div class="controls"> -->
<!-- 					<input type="checkbox" name="terms" id="terms"> -->
<!-- 				</div> -->
<!-- 			</div>		 -->
			<div class="control-group">
				<div class="controls">
					<button type="submit" class="btn btn-success">Register</button>
				</div>
			</div>				
		{form_close()}			
	</div>
	<div class="span5">
		<h2>{lang('why_register')}</h2>
		<div>
			{lang('why_register_text')}
		</div>
	</div>	
</div>