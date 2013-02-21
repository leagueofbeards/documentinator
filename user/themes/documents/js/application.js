/*
function noError() { return true; }
window.onerror = noError;
*/

var shown = false;
var loaded = false;

$(document).ready(function() {
	styleCode();
	setupPermissions();
	annotate();
	
	$('#intro').click(function() {
		if( $(this).attr('contenteditable') === 'false' ) {
			annotate();
		}
	});

	$('#projects').mouseenter(function() {
		if( shown == false ) {
			$.get( DI.url + '/auth_ajax/get_documents?current=' + $(this).data('current'), function(d) {
				if( d.response_code = 200 ) {
					$('#projects').append( d.data );
					shown = true;
				}
			});
		}
	});
	
	$('body').click(function() {
		$('#projects').find('#choices').remove();
		shown = false;
	});
	
	$(".save").on('click', function() {
		if( $('.editable').attr('contenteditable') == 'true' ) {
			var content = $('.editable').html();
			var title = $('.article header h1').html();
			var data = $('#update_doc').serializeArray();
			var obj = new Object();
			obj.content = content;
			obj.title = title;
			var url = $('.inplace').attr('action');
			$.post( url, obj, handleResponse  );
			styleCode();
			// history.pushState(null, null, '/some-path');
			return false;
		} else {
			$('.inplace').load( DI.page + ' #intro', function() {
				loaded = false;
				$('#editor').fadeIn();
				$('.editable').attr('contenteditable', true);
				$('.article header h1').attr('contenteditable', true);
				$('.editable').attr('contenteditable', true);
				$('#save .save').addClass('update').html('<i class="icon-save">s</i>');
			});

			return false;
		}
	});
	
	$('.editable, .article header h1').keydown(function() {
		$('#save .save').addClass('update').html('<i class="icon-save">s</i>');
	});
	
	$('#add_document').click(function() {
		var url  = $('#new_document').attr('action');
		var args = $('#new_document').serialize();
		$.post( url, args, handleResponse );
		return false;
	});

	$('#add_contributor').on('click', function() {
		$('#invite_submit').fadeIn();
		$('#invitee').focus();
		return false;
	});

	$('#invite_submit').submit(function() {
		var args = $(this).serialize();
		var url = $(this).attr('action');	
		$('#invitee').val('');
		$.post( url, args, handleResponse );
		return false;
	});

	$('form.ajax').submit(function() {
		var args = $(this).serialize();
		var url = $(this).attr('action');	
		$.post( url, args, handleResponse );
		
		return false;
	});

	$('body').on('click', 'a.wsse', function() {
		$(this).querystring(DI.WSSE);
	});

	window.setInterval(function() {
		var dep = $.get(DI.WSSE_update);
		dep.done(function(response) {
			$.extend(DI.WSSE, response);
		});
	}, 60000);

	$('.wsse').on('click', function() {
		var url = $(this).data('url');
		$.post( url, null, handleResponse );
		
		return false;
	});

	$('#heading').hover(function() {
		$('#submenu').fadeIn();
	});

	$('#submenu').mouseleave(function() {
		$(this).stopTime();
		$(this).oneTime(3000, function() { $('#submenu').fadeOut(); });
	})
});

var handleResponse = function(data, callback) {
	if(data.response_code == undefined) {
	} else {
		if(data.response_code != 200) {
			// @todo Do something different on error?
		}
				
		if(data.message != undefined && data.message != '') {
			displayMessage( data.message )
		}
		
		if(data.habari_callback != undefined) {
			eval(data.habari_callback);
		}
		
		var semaphore = 0;
		var all = false;
		
		for(var i in data.html) {
			var value = data.html[i];
			if(value == '#') {
				semaphore++
				$(i).hide().load(location.href + ' #' + $(i).attr('id') + ' > *', function() {
					setupPermissions(data.data);
					semaphore--;
				}).fadeIn();
			}
			else {
				$(i).html(value);
			}
		}
	}
	
	all = true;
}

var handlePermissionsRepsonse = function(data, callback) {
	if(data.response_code == undefined) {
	} else {
		if(data.response_code != 200) {
			// @todo Do something different on error?
		}
				
		if(data.message != undefined && data.message != '') {
			displayMessage( data.message )
		}
		
		if(data.habari_callback != undefined) {
			eval(data.habari_callback);
		}
		
		var semaphore = 0;
		var all = false;
		
		for(var i in data.html) {
			var value = data.html[i];
			if(value == '#') {
				semaphore++
				$(i).hide().load(location.href + ' #' + $(i).attr('id') + ' > *', function() {
					setupPermissions(data.data);
					semaphore--;
					if(all && semaphore == 0 && typeof callback == "function") {
						callback();
					}
				}).fadeIn();
			}
			else {
				$(i).html(value);
			}
		}
	}
	
	all = true;
}

var styleCode = function() {
	var a = false;

	$('pre').each(function() {
		if (!$(this).hasClass("prettyprint")) {
			$(this).addClass("prettyprint");
			a = true
		}
	});
	
	if (a) { prettyPrint() } 
}

var displayMessage = function(message) {
	human_msg.display_msg( message );
}

var setupPermissions = function(user) {
	$('#participating li.update').each(function() {
		var url = $(this).data('url');
		$(this).popover({
			animation: true,
			title: 'Set Permissions',
			html: true,
			content: '<p>Update this user\'s permissions below.</p><form><select name="permissions" id="permissions" onchange="savePermissions(this.value);"><option>Choose Permissions</option><option value="' + url + '&perm=1">Reviewer</option><option value="' + url + '&perm=2">Editor</option></select></form>',
			placement: 'right',
			trigger: 'click',
		});
		
		if( user != null && $(this).attr('id') == 'user-' + user ) {
			$(this).trigger('click');
		}
	});
}

var savePermissions = function(url) {
	$.get( url, handlePermissionsRepsonse );
}

var annotate = function() {
	options = {
		user: { 
			username: DI.username
		},
		store: {
			prefix: DI.url + '/v1',
			urls: {
				create:  '/create/annotation',
				read:    '/read/annotations/:id',
				update:  '/update/annotation/:id',
				destroy: '/destroy/annotation/:id'
			}
		}
	}
		
	if( $('#intro').attr('contenteditable') === 'false' && loaded === false ) {
		var entry = $('#intro').annotator();
			entry.annotator('addPlugin', 'Store', options.store );
			entry.annotator('addPlugin', 'Avatar' );
			loaded = true;
	}
}