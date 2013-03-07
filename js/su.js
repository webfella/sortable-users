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
		},
		stop: function(e, ui) {
			var rows = ui.item.parent().find('> tr'),
			rowClass = 'alternate';
			rows.each(function(i, v) {
				var user = jQuery(this),
				userData = jQuery('td.user_order .su_sort', user),
				indexPlusOne = user.index() + 1;
				jQuery.ajax({
					url: sortable_users_data.ajax_url,
					type: 'POST',
					data: {
						action:'my_action',
						user_ID: userData.attr('data-su-user-id'),
						user_value: indexPlusOne
					},
					async: false
				});
				userData.text(indexPlusOne);
				rows.removeClass(rowClass).filter(':even').addClass(rowClass);
			});
		}
	});
});