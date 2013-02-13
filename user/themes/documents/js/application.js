var shown = false;
$(document).ready(function() {	
	styleCode();
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
			unstyleCode();
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
			$('#editor').fadeIn();
			$('.article header h1').attr('contenteditable', true);
			$('.editable').attr('contenteditable', true);
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

var unstyleCode = function() {
	var a = false;

	$('pre').each(function() {
		if ($(this).hasClass("prettyprint")) {
			$(this).removeClass("prettyprint");
			a = true
		}
	});
	
	if (a) { prettyPrint() } 
}

var displayMessage = function(message) {
	human_msg.display_msg( message );
}

/*
var annotate = function(range) {
	range = decodeURI(range);
	var html = $('.editable').html();
	$('body').focus();
}

$('.editable').mouseup(function() {
	var range = document.getSelection().getRangeAt(0);
	var newNode = document.createElement('mark');
	range.surroundContents( newNode );
	$('body').focus();
	var query = {};
	$.extend(query, DI.WSSE);
	query.user_id = DI.user_id;
	query.post_id = DI.post_id;
	query.range_text = encodeURI(range);
	
	if( range && (range = new String(range).replace(/^\s+|\s+$/g,'')) ) {
		url = DI.url + '/auth_ajax/save_selection';
		$.post( url, query, handleResponse );
	}
});

$(window).load(function() {
	if( DI.post_id != '' ) {
		url = DI.url + '/auth_ajax/get_selections';
		var query = {};
		query.post_id = DI.post_id;
		$.get( url, query, function(d) {
			var notes = $.parseJSON( d );
			for( var i in notes ) {
				annotate( notes[i].range_text );
			}
		});
	}
});
*/