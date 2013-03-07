jQuery(document).ready(function() {
	jQuery('table.users tbody').sortable({
		items: '> tr'
	});
});