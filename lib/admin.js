$(document).ready(function () {
	//allows that tables to be sortable
	//$("#userTable, #tripsTable, #sheltersTable, #alertsTable").tablesorter();
	//allows that tables to be sortable with the datatable plugin
	$('#alertsTable').dataTable({
		"bJQueryUI": true,
        "sPaginationType": "full_numbers",
        "aaSorting": [[ 0, "desc" ]],
		"bAutoWidth": false,
		"aoColumns": [{"sWidth":"20%"},{"sWidth":"20%"},{"sWidth":"40%"},{"sWidth":"20%"}],
		"iDisplayLength": 10
	});

	$('#usersTable, #tripsTable').dataTable({
		"bJQueryUI": true,
        "sPaginationType": "full_numbers",
        "aaSorting": [[ 1, "asc" ]],
		"iDisplayLength": 10
	});

	$('#sheltersTable').dataTable({
		"bJQueryUI": true,
        "sPaginationType": "full_numbers",
        "aaSorting": [[ 0, "asc" ]],
		"iDisplayLength": 10
	});

	$('#sheltersTableHome, #usersTableHome').dataTable({
		"bJQueryUI": true,
        "sPaginationType": "full_numbers",
        "aaSorting": [[ 0, "asc" ]],
		"iDisplayLength": 10,
		"bFilter": false,
		"bInfo": false,
		"bPaginate": false
	});
	
	$('#urgentTableHome').dataTable({
		"bJQueryUI": true,
        "sPaginationType": "full_numbers",
        "aaSorting": [[ 0, "asc" ]],
		"iDisplayLength": 10,
		"bFilter": false,
		"bInfo": false,
		"bPaginate": false,
		"bAutoWidth": false,
		"aoColumns": [{"sWidth":"13%"},{"sWidth":"13%"},{"sWidth":"13%"},{"sWidth":"13%"},{"sWidth":"48%"}]
	});

			
	//pops up a modal window with class=more
	$('.more').click(function (e) {
		$.modal('<iframe src="'+this.href+'" height="500" width="600" style="border:0"></iframe>', 
		{overlayClose:true, 
		onClose: function() {
			window.location.reload(true);
			}
		});
		return false;
	});
	
	//setup validation on these forms
	$("#adminform").validate();


	//pops up a map window with class=map
	$('.map').click(function (e) {
		$.modal('<iframe src="'+this.href+'&output=embed" height="450" width="650" style="border:0"></iframe>', {overlayClose:true});
		return false;
	});
});