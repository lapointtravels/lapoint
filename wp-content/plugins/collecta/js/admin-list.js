;(function ($, window, document, undefined) {

	var totalCount = parseInt($("#collect-user-table").attr("data-total-count"), 10),
		$paginationContainer = $("#collecta-user-pagination"),
		$tbody = $("#collect-user-table tbody"),
		settings = {
			action: 'collecta-fetch-users',
			sort: 'name',
			order: 'asc',
			page: 1,
			per_page: 50
		},
		pages = Math.ceil(totalCount / settings.per_page);

	$(document).ready(function($){

		$("#collect-user-table th").on('click', function(){
			var sort = $(this).attr("data-sort");
			if (sort == settings.sort){
				settings.order = (settings.order == 'asc') ? 'desc' : 'asc';
			} else {
				settings.order = 'asc';
			}
			updateContent({
				sort: sort
			});
		});

		// Setup pagination links
		if (pages > 1){
			for (var i=1; i<=pages; i++){
				$paginationContainer.append(
					$('<a>').attr("id", "page-"+ i).text(i)
				);
			}
			$paginationContainer.find('a').on('click', function(){
				var page = parseInt($(this).text(), 10);
				updateContent({
					page: page
				});
			});	
		}

		updateDisplay();
	});

	function updateContent(options){
		$.extend(settings, options);

		$tbody.empty().append(
			$("<td>").addClass("loading").attr("colspan", 5).text("Loading...")
		);
		updateDisplay();

		// Fetch users
		$.get(ajaxurl, settings, function(data){
			if (data && data.status == 200){
				var users = data.data.users, user;
				$tbody.empty();

				for (var i=0; i<users.length; i++){
					user = users[i];
					$tbody.append(
						$('<tr>')
							.append( $('<td>').text(user.name) )
							.append( $('<td>').text(user.email) )
							.append( $('<td>').text(user.lang) )
							.append( $('<td>').text(user.ip) )
							.append( $('<td>').text(user.created) )
					);
				}
			}
		});
	}
	function updateDisplay(){
		// Update sort and order display
		$("#collect-user-table th").removeClass("sort").removeClass("asc").removeClass("desc");
		$("#collect-user-table th[data-sort='"+ settings.sort +"']").addClass("sort").addClass(settings.order);

		// Update pagination
		$paginationContainer.find("a").removeClass("active");
		$paginationContainer.find("a#page-"+ settings.page).addClass("active");
	}

}(jQuery, window, document));