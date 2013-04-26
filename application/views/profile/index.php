<div class="row">
	<div class="span12">
		<h2>{$user->username|capitalize}</h2>
	</div>
</div>
<div class="row">
	<div class="span9">
		<h3>Your characters</h3>
	</div>
	<div class="span3">
		<a class="btn btn-success pull-right topright" href="{site_url('char/create')}"><i class="icon-plus-sign icon-white"></i> New character</a>
	</div>
</div>
<div class="row">
	<div class="span9">
		<table class="table table-hover">
			<thead>
				<tr>
					<th>Name</th>
					<th>Level</th>
					<th>Race</th>
					<th>Classes</th>
				</tr>
			</thead>
			<tbody>
			{foreach from=$account->characters item=character}
				<tr>
					<td><a href="{site_url('char/index')}/{charlink($character)}">{$character->name}</a></td>
					<td>13</td>
					<td>Human</td>
					<td>Fighter(4), Ranger(9)</td>
				</tr>
			{/foreach}
			</tbody>
		</table>
	</div>
	<div class="span3">
		<p>Lorem Ipsum is simply dummy text of the printing and typesetting industry. Lorem Ipsum has been the industry's standard dummy text ever since the 1500s, when an unknown printer took a galley of type and scrambled it to make a type specimen book. It has survived not only five centuries, but also the leap into electronic typesetting, remaining essentially unchanged. It was popularised in the 1960s with the release of Letraset sheets containing Lorem Ipsum passages, and more recently with desktop publishing software like Aldus PageMaker including versions of Lorem Ipsum.</p>
	</div>
</div>