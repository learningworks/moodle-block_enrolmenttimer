var options = [];
var arrayKeys = [];
var timestamp = 0;

$(document).ready(function() {
	console.log('loaded');
	if($('.block_enrolmenttimer .active').length > 0){
		getDisplayedOptions();
		populateWithData();
		makeTimestamp();

		//create timer
		window.setInterval(function(){
			updateLiveCounter();
		}, 1000);
	}
});

function getDisplayedOptions(){
	var children = $('.block_enrolmenttimer .active .timer-wrapper').children('.timerNum');

	for (var i = children.length - 1; i >= 0; i--) {
		var arrayKey = $(children[i]).attr('data-id');
		arrayKeys.push(arrayKey);
	};
}

function populateWithData(){
	var counts = [];

	for (var i = arrayKeys.length - 1; i >= 0; i--) {
		var option = $('.block_enrolmenttimer .active .text-desc .'+arrayKeys[i]).text();
		options[arrayKeys[i]] = option;
	};
}

function makeTimestamp(){
	for (var i = arrayKeys.length - 1; i >= 0; i--) {
		switch(arrayKeys[i]){
			case 'seconds':
				timestamp += parseInt(options[arrayKeys[i]], 10);
				break;

			case 'minutes':
				timestamp += parseInt(options[arrayKeys[i]], 10) * 60;
				break;

			case 'hours':
				timestamp += parseInt(options[arrayKeys[i]], 10) * 3600;
				break;

			case 'days':
				timestamp += parseInt(options[arrayKeys[i]], 10) * 86400;
				break;

			case 'weeks':
				timestamp += parseInt(options[arrayKeys[i]], 10) * 604800;
				break;

			case 'months':
				timestamp += parseInt(options[arrayKeys[i]], 10) * 2592000;
				break;

			case 'years':
				timestamp += parseInt(options[arrayKeys[i]], 10) * 31536000;
				break;
		}
	};
	console.log('time -' + timestamp);
}

function updateLiveCounter(){
	console.log(arrayKeys);
	timestamp--;
	var time = timestamp;
	var tokens = ['years', 'months', 'weeks', 'days', 'hours', 'minutes', 'seconds'];
	var units = ['31536000', '2592000', '604800', '86400', '3600', '60', '1'];

	for (var i = 0; i <= tokens.length; i++) {
		if($.inArray(tokens[i], arrayKeys)){
			if(time > units[i]){
				var count = Math.floor(time / units[i]);
				updateMainCounter(tokens[i], count);
				time = time - (count*units[i]);
			}
		}
	};
}

function updateMainCounter(counter, time){
	// var html = '';
	// for (var i = 0; i < time.length; i++) {
	// 	html += '<span class="timerNumChar" data-id="'+ i +'">'+ time[i] +'</span>';
	// };
	// $('.block_enrolmenttimer .active .timer-wrapper .timerNum[data-id='+counter+']').html(html);
	$('.block_enrolmenttimer .active .text-desc .'+counter).html(time);
}