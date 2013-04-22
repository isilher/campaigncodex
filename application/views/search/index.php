<div class="row">
	<div class="span12">
		<h2>{$title}</h2>
	</div>
</div>
<div class="row">
	<div class="span12">
		<p>Found {$characters->result_count()} characters</p>
		<table class="table table-hover">
			<thead>
				<tr>
					<th>Name</th>
					<th>Level</th>
					<th>Race</th>
					<th>Classes</th>
					<th>Player</th>
				</tr>
			</thead>
			<tbody>
			{foreach from=$characters item=character}
				<tr>
					<td><a href="{site_url('char/index')}/{$character->name}-{$character->unique}">{$character->name}</a></td>
					<td>13</td>
					<td>Human</td>
					<td>Fighter(4), Ranger(9)</td>
					<td><a href="{site_url('player/maglok')}">Maglok</a></td>
				</tr>
			{/foreach}
			</tbody>
		</table>
	</div>
</div>