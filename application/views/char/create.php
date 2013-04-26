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
			<div class="control-group{if form_error('charname')} error{/if}">
				<label class="control-label" for="type">{lang('create_charname')} *</label>
				<div class="controls">
					<input type="text" name="charname" id="value" placeholder="Enter character name" value="{set_value('charname')}">
				</div>
			</div>					
			<div class="control-group">
				<div class="controls">
					<button type="submit" class="btn btn-success">Create</button>
				</div>
			</div>				
		{form_close()}			
	</div>
	<div class="span5">
		<h2>{lang('create_explanation')}</h2>
		<div>
			{lang('create_explanation_text')}
		</div>
	</div>	
</div>