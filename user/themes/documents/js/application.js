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
			
	$("#save").click(function (e) {
		var content = $('.editable').html();
		console.log(content);
		return false;
	});
});

var handleResponse = function(data, callback) {
	if(data.response_code == undefined) {
		console.log(data);
	} else {
		if(data.response_code != 200) {
			// @todo Do something different on error?
		}
		
		if(data.message != undefined && data.message != '') {
			$('.error').html( data.message ).fadeIn();
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
