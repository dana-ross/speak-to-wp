jQuery(function () {

	if (annyang) {

		// Let's define our first command. First the text we expect, and then the function it should call
		var commands = {
			'search for *query': search_for,
			'read *title'      : read_post,
			'log in'           : log_in,
			'log me in'        : log_in,
			'log out'          : log_out,
			'log me out'       : log_out
		};

		// Add our commands to annyang
		annyang.addCommands(commands);

		// Start listening. You can call this here, or attach this call to an event, button, etc.
		annyang.start();

	}

	function search_for(query) {

		jQuery.get(
				AnnyangSettings.ajax_url,
				{
					'action'            : 'speaktowp',
					'speak-to-wp-action': 'sanitize-search',
					'search-query'      : query
				},
				function (data) {
					if (data.success) {
						window.location.href = AnnyangSettings.home_url + '?s=' + data.data;
					}
				}
		);

	}

	function read_post(title) {

		jQuery.get(
				AnnyangSettings.ajax_url,
				{
					'action'            : 'speaktowp',
					'speak-to-wp-action': 'post-by-title',
					'search-query'      : title
				},
				function (data) {
					if (data.success) {
						window.location.href = data.data;
					}
				}
		);


	}

	function log_in() {
		window.location.href = AnnyangSettings.login_url;
	}

	function log_out() {
		window.location.href = AnnyangSettings.logout_url;
	}

});