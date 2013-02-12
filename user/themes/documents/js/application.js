$(document).ready(function() {
	$(".page").mouseenter(function() {
		$('#editor').fadeIn();
	});
	
	$('.editable').keydown(function() {
		$('#save').addClass('enabled');
		$(this).stopTime();
		$(this).oneTime(2000, function() { stylePreview(); });
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
		$('#invitee').val('');
		console.log(args);
		return false;
	});

	$("#save").click(function (e) {
		var content = $('.editable').html();
		var title = $('header h1').html();
		var data = $('#update_doc').serializeArray();
		var obj = new Object();
		obj.content = content;
		obj.title = title;
		var url = $('.inplace').attr('action');
		$.post( url, obj, handleResponse  );
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

	$('a.wsse').on('click', function() {
		var url = $(this).attr('href');
		$.post( url, null, handleAjaxResponse );
		
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
		console.log(data);
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
			if(value == '#') { // refresh the contents of this element from the original page
				semaphore++
				$(i).hide().load(location.href + ' #' + $(i).attr('id') + ' > *', function(){
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

	$('pre').parent().each(function() {
		if (!$(this).hasClass("prettyprint")) {
			$(this).addClass("prettyprint");
			a = true
		}
	});
	
	if (a) { prettyPrint() } 
}

var stylePreview = function() {
	$('.editable pre').each(function() {
		$(this).html( prettyPrintOne($(this).html()) );
	});
}

var displayMessage = function(message) {
	human_msg.display_msg( message );
}