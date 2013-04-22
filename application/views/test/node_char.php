<h2>Manage character: {$character}</h2>
{if $message != ''}
	 <div class="alert alert-success"><button class="close" data-dismiss="alert" type="button">x</button><strong class="text-success">Success!</strong><br>{$message}</div>
{/if}
{if $error != ''}
	<div class="alert alert-error"><strong class="text-success">Error!</strong><br>{$message}</div>
{/if}
<span class="span5">
	{form_open(current_url(), $classes_form)}
		{form_hidden('action', 'add_basic')}
		<div class="alert"><strong>Note!</strong> For a new character make <strong>one</strong> 'Prime' type note and set the value to 'prime' or crashes <strong>will</strong> occur!</div>	
		<div class="control-group{if form_error('type')} error{/if}">
			<label class="control-label" for="type">Type *</label>
			<div class="controls">
				{form_dropdown('type', $type_options, '', 'id="type" name="type"')} {form_error('type')}
			</div>
		</div>
		<div class="control-group{if form_error('value')} error{/if}">
			<label class="control-label" for="value">Value *</label>
			<div class="controls">
				<input type="text" name="value" id="value" placeholder="Value"> {form_error('value')}
			</div>
		</div>
		<div class="control-group{if form_error('parent')} error{/if}">
			<label class="control-label" for="parent">Parent</label>
			<div class="controls">
				{form_dropdown('parent', $parent_options, {$prime->id}, 'id="parent" name="parent"')} {form_error('parent')}
			</div>
		</div>	
		<div class="control-group">
			<div class="controls">
				<button type="submit" class="btn btn-success">Create node</button>
			</div>
		</div>	
	{form_close()}
</span>

<span class="span7">
	<h2>Character</h2>
	<div class="alert"><strong>Note!</strong> Deleting any node will also delete it's children. This listing will go five nodes deep. Any deeper is not displayed.</div>
	<ul>
	{foreach from=$prime->children item=child}
		<li>NODE: {$child->id}, TYPE: {$child->type}, VALUE: {$child->value} <a href="{site_url('test/node_delete')}/{$child->id}"><i class="icon-trash"></i></a>
		{if $child->children->exists()}
			<ul>
			{foreach from=$child->children item=child2}
				<li>NODE: {$child2->id}, TYPE: {$child2->type}, VALUE: {$child2->value} <a href="{site_url('test/node_delete')}/{$child2->id}"><i class="icon-trash"></i></a>
				{if $child2->children->exists()}
					<ul>
					{foreach from=$child2->children item=child3}
						<li>NODE: {$child3->id}, TYPE: {$child3->type}, VALUE: {$child3->value} <a href="{site_url('test/node_delete')}/{$child3->id}"><i class="icon-trash"></i></a>
						{if $child3->children->exists()}
							<ul>
							{foreach from=$child3->children item=child4}
								<li>NODE: {$child4->id}, TYPE: {$child4->type}, VALUE: {$child4->value} <a href="{site_url('test/node_delete')}/{$child4->id}"><i class="icon-trash"></i></a>
								{if $child4->children->exists()}
									<ul>
									{foreach from=$child4->children item=child5}
										<li>NODE: {$child5->id}, TYPE: {$child5->type}, VALUE: {$child5->value} <a href="{site_url('test/node_delete')}/{$child5->id}"><i class="icon-trash"></i></a>
										</li>
									{/foreach}
									</ul>
								{/if}</li>								
								</li>
							{/foreach}
							</ul>
						{/if}</li>
					{/foreach}
					</ul>
				{/if}</li>
			{/foreach}
			</ul>
		{/if}</li>
	{/foreach}
	</ul>
</span>