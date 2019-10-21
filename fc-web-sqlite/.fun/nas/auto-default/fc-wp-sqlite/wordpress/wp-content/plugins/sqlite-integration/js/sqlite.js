/**
 * This is a part of SQLite Integration.
 * 
 * This script is only included on the documentation and utility page.
 * 
 * @package SQLite Integration
 * @author Kojima Toshiyasu
 */
jQuery(document).ready(function($) {
	var $table = null;
	var $headers = null;
	if (document.getElementById("sqlite-table") != null) {
		$table = $('#sqlite-table');
		$headers = $table.find('thead th').slice(0,2);
	} else if (document.getElementById("plugins-table") != null) {
		$table = $('#plugins-table');
		$headers = $table.find('thead th').slice(0);
	} else if (document.getElementById("patch-files") != null) {
		$table = $('#patch-files');
		$headers = $table.find('thead th').slice(1);
	} else if (document.getElementById("backup-files") != null) {
		$table = $('#backup-files');
		$headers = $table.find('thead th').slice(1);
	}
	$headers
		.wrapInner('<a href="#"></a>')
		.addClass('sort');
	var rows = $table.find('tbody > tr').get();
	$headers.bind('click', function(event) {
		event.preventDefault();
		var $header = $(this),
			sortKey = $header.data('sort').key,
			sortDirection = 1;
		if ($header.hasClass('sorted-asc')) {
			sortDirection = -1;
		}
		rows.sort(function(a, b) {
			var keyA = $(a).data('table')[sortKey];
			var keyB = $(b).data('table')[sortKey];
			if (keyA < keyB) return -sortDirection;
			if (keyA > keyB) return sortDirection;
			return 0;
		});
		$headers.removeClass('sorted-asc sortd-desc');
		$headers.addClass(sortDirection == 1 ? 'sorted-asc' : 'sorted-desc');
		$.each(rows, function(index, row) {
			$table.children('tbody').append(row);
		});
		stripe('#plugins-table');
		stripe('#sqlite-table');
		stripe('#patch-files');
	});
	function stripe(arg) {
		$(arg).find('tr.alt').removeClass('alt');
		var $args = arg + ' tbody';
		$($args).each(function() {
			$(this).children(':visible').has('td').filter(function(index) {
				return (index % 2) == 1;
			}).addClass('alt');
		});
	}
	stripe('#plugins-table');
	stripe('#sys-info');
	stripe('#sqlite-table');
	stripe('#status');
	stripe('#patch-files');
});

jQuery(document).ready(function($) {
  var $table = $('#plugins-info');
	var $headers = $table.find('thead th').slice(0);
	$headers
		.wrapInner('<a href="#"></a>')
		.addClass('sort');
	var rows = $table.find('tbody > tr').get();
	$headers.bind('click', function(event) {
		event.preventDefault();
		var $header = $(this),
			sortKey = $header.data('sort').key,
			sortDirection = 1;
		if ($header.hasClass('sorted-asc')) {
			sortDirection = -1;
		}
		rows.sort(function(a, b) {
			var keyA = $(a).data('table')[sortKey];
			var keyB = $(b).data('table')[sortKey];
			if (keyA < keyB) return -sortDirection;
			if (keyA > keyB) return sortDirection;
			return 0;
		});
		$headers.removeClass('sorted-asc sortd-desc');
		$headers.addClass(sortDirection == 1 ? 'sorted-asc' : 'sorted-desc');
		$.each(rows, function(index, row) {
			$table.children('tbody').append(row);
		});
		stripe('#plugins-info');
	});
	function stripe(arg) {
		$(arg).find('tr.alt').removeClass('alt');
		var $args = arg + ' tbody';
		$($args).each(function() {
			$(this).children(':visible').has('td').filter(function(index) {
				return (index % 2) == 1;
			}).addClass('alt');
		});
	}
	stripe('#plugins-info');
});
