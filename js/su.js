jQuery(document).ready(function() {
	jQuery('table.users tbody').sortable({
		items: '> tr',
		helper: function(e, tr) {
			var originals = tr.children(),
			helper = tr.clone();
			helper.children().each(function(index) {
				jQuery(this).width(originals.eq(index).width());
			});
			return helper;
		}
	});
});