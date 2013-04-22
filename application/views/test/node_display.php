<table class="table table-hover">
	<thead>
		<tr>
			<th>#</th>
			<th>Type</th>
			<th>Value</th>
			<th>Created on</th>
			<th>Updated on</th>
		</tr>
	</thead>
	<tbody>
	{foreach from=$character->nodes item=node}
		<tr>
			<td>{$node->id}</td>
			<td>{$node->type}</td>
			<td>{$node->value}</td>
			<td>{$node->created_on}</td>
			<td>{$node->updated_on}</td>
		</tr>
	{/foreach}
	</tbody>
</table>