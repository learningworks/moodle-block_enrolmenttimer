// This file is part of Moodle - http://moodle.org/
//
// Moodle is free software: you can redistribute it and/or modify
// it under the terms of the GNU General Public License as published by
// the Free Software Foundation, either version 3 of the License, or
// (at your option) any later version.
//
// Moodle is distributed in the hope that it will be useful,
// but WITHOUT ANY WARRANTY; without even the implied warranty of
// MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
// GNU General Public License for more details.
//
// You should have received a copy of the GNU General Public License
// along with Moodle.  If not, see <http://www.gnu.org/licenses/>.

/**
 * @package    block_enrolmenttimer
 * @copyright  2014 onwards LearningWorks Ltd {@link https://learningworks.co.nz/}
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 * @author     Aaron Leggett
 */

/**
 * @module block_enrolmenttimer/scripts
 */
define(['jquery'], function($) { // Moodle needs this to recognise $ https://docs.moodle.org/dev/jQuery .
    // JQuery is available via $.

    return {
        initialise: function() {
            // Module initialised.
            $(document).ready(function() {
                var options = [];
                var arrayKeys = [];
                var timestamp = 0;
                var forceTwoDigits = false;

                function getDisplayedOptions() {
                    var children = $('.block_enrolmenttimer .active .timer-wrapper').find('.timerNum');

                    for (var i = children.length - 1; i >= 0; i--) {
                        var arrayKey = $(children[i]).attr('data-id');
                        arrayKeys.push(arrayKey);
                    }
                }

                function populateWithData() {
                    for (var i = arrayKeys.length - 1; i >= 0; i--) {
                        var option = $('.block_enrolmenttimer .active .text-desc .' + arrayKeys[i]).text();
                        options[arrayKeys[i]] = option;
                    }
                }

                function makeTimestamp() {
                    for (var i = arrayKeys.length - 1; i >= 0; i--) {
                        switch (arrayKeys[i]) {
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
                    }
                }

                function updateMainCounter(counter, time) {
                    var html = '';
                    if (forceTwoDigits === true && time.toString().length == 1) {
                        html += '<span class="timerNumChar" data-id="0">0</span>';
                        html += '<span class="timerNumChar" data-id="1">' + time.toString() + '</span>';
                    } else {
                        for (var i = 0; i < time.toString().length; i++) {
                            html += '<span class="timerNumChar" data-id="' + i + '">' + time.toString().charAt(i) + '</span>';
                        }
                    }

                    $('.block_enrolmenttimer .active .timer-wrapper .timerNum[data-id="' + counter + '"]').html(html);
                    $('.block_enrolmenttimer .active .text-desc .' + counter).html(time);
                }

                function updateLiveCounter() {
                    timestamp--;
                    var time = timestamp;
                    var tokens = ['years', 'months', 'weeks', 'days', 'hours', 'minutes', 'seconds'];
                    var units = ['31536000', '2592000', '604800', '86400', '3600', '60', '1'];

                    for (var i = 0; i < tokens.length; i++) {

                        if (arrayKeys.indexOf(tokens[i]) != -1) {
                            if (time >= units[i]) {
                                var count = Math.floor(time / units[i]);
                                updateMainCounter(tokens[i], count);
                                time = time - (count * units[i]);
                            } else {
                                updateMainCounter(tokens[i], 0);
                            }
                        }
                    }
                }

                if ($('.block_enrolmenttimer .active').length > 0) {
                    getDisplayedOptions();
                    populateWithData();
                    makeTimestamp();

                    // Create timer.
                    window.setInterval(function() {
                        updateLiveCounter();
                    }, 1000);
                }

                if ($('.block_enrolmenttimer .timer-wrapper[data-id=force2]').length > 0) {
                    forceTwoDigits = true;
                }
            });
        }
    };
});