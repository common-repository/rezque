jQuery(function($) {
	$(document).ready(function() {

		var current_fs, next_fs, previous_fs; //fieldsets

		$("#rezque .previous").click(function(){
			current_fs = $(this).parent();
			current_fs.hide();
			previous_fs = $(this).parent().prev();
			previous_fs.fadeIn(500); 
		});

		$("#rezque").submit(function(e){
	        e.preventDefault();
	        $('.spinner-outer-wrapper').show();
			var name = $("#rezque input#name").val(),
            email = $("#rezque input#email").val(),
            phone = $("#rezque input#phone").val(),
            address = $("#rezque input#address").val(),
            day = $("#rezque input#day").val(),
            start = $("#rezque input#start").val(),
            end = $("#rezque input#end").val(),
            hof = $("#rezque input[name=hof]:checked").val(),
            calendar = $("#rezque input#calendar").val(),
            date_format = $("#rezque input#date_format").val(),
            ip = $("#rezque input#ip").val(),
            referer = $("#rezque input#referer").val(),
            source = $("#rezque input#source").val(),
            medium = $("#rezque input#medium").val(),
            campaign = $("#rezque input#campaign").val(),
            content = $("#rezque input#content").val();

            $.ajax({
                url: "https://app.rezque.com/appointments.json",
                type: "POST",
                dataType: "json",
                data: { 
                    appointment: {
                        day: day,
                        start: start,
                        end: end,
                        hof: hof,
                        name: name,
                        email: email,
                        phone: phone,
                        address: address,
                        calendar_id: calendar,
                        calendar: {
                            date_format: date_format,
                        },
                        analytics_attributes: {
                            ip: ip,
                            referer: referer,
                            source: source,
                            medium: medium,
                            campaign: campaign,
                            content: content,
                        }
                    }
                },
                cache: false,
                success: function() {
                    window.location = rezque_success.redirect_url;
                },
                error: function(data) {
                	$('.spinner-outer-wrapper').hide();
                    var notices = JSON.parse(data.responseText);
                    $('#rezque .notices').empty();
                    for (var i in notices) {
                      $('#rezque .notices').append(notices[i] + "<br>");
                    }
                },
            })
		});

        waitForGetDays();

        var calendar_id = $('#rezque #calendar').val(),
        date = new Date(),
        calendar_month = getDays(date.getFullYear(),date.getMonth()+1,calendar_id),
        calendar_week = [];

        function getDays(year,month,calendar_id){
            $.ajax({
                url: "https://app.rezque.com/appointments/get_days.json",
                cache: false,
                data: {year:year,month:month,calendar_id:calendar_id},
                success: function(data){
                    calendar_month = eval(data);
                    $('#rezque #date').datepicker("refresh");
                },
                error: function(data){
                    $("form#rezque").replaceWith("<p>There appears to be an error. Try again shortly.</p>");
                }
            });
        }

        function getSlots(year,month,day,calendar_id){
            $.ajax({
                url: "https://app.rezque.com/appointments/get_slots.json",
                cache: false,
                data: {year:year,month:month,day:day,calendar_id:calendar_id},
                success: function(data){
                    calendar_week = eval(data);
                    $('#rezque .date-slots').html('');
                    displaySlots(calendar_week);
                }
            });
        }

        function displaySlots(object) {
            slots = object["open_slots"];
            counter = 0
            for (var k in slots){
                var d = new Date(k),
                weekday =  ["Sunday","Monday","Tuesday","Wednesday","Thursday","Friday","Saturday"],
                header = '<div class="'+k+' row"><div class="col-12"><span class="dow">' + weekday[d.getDay()] + '</span><br><span class="dow">' + formatDate(k,calendar_month['date_format']) + '</span></div><br></div><hr>';
                $('.date-slots').append(header);
                for (var v in slots[k]){
                    var d = new Date(),
                    hm = v.split(":");
                    hours = d.setHours(hm[0],hm[1])
                    minutes = d.setMinutes(d.getMinutes() + object["duration"]);
                    end = ('0' + d.getHours()).slice(-2) + ':' + ('0' + d.getMinutes()).slice(-2);

                    if (slots[k][v] > 0) {
                        var timeSlot = '<div id="' + formatDate(k,calendar_month['date_format']) + '|' + v + '|' + end + '" class="col-6"><a class="time-select" href="#">' + formatTime(v,calendar_month['time_format']) + ' to ' + formatTime(end,calendar_month['time_format']) + '</a></div>';
                        $('.' + k).append(timeSlot);
                    }
                }
                counter++
                break;
            }

            $('#rezque a.time-select').click(function(e) {
                e.preventDefault();
                var chosenSlot = $(this).parent().attr('id').split('|');
                day = chosenSlot[0]
                start = chosenSlot[1];
                end = chosenSlot[2];
                $('#rezque input#day').val(day);
                $('#rezque #date').datepicker( "setDate", day );
                $('#rezque input#start').val(start);
                $('#rezque input#end').val(end);
                $('#rezque .fs-subtitle').html('Date: ' + day + ', Time: ' + start + ' to ' + end);

                current_fs = $(this).parents('fieldset');
                current_fs.hide();
                next_fs = $(this).parents('fieldset').next();
                next_fs.fadeIn(500);
            });
        }

        function available(date) {
            var ymd = date.getFullYear() + "-" + ('0' + (date.getMonth()+1)).slice(-2) + "-" + ('0' + date.getDate()).slice(-2);
            if ($.inArray(ymd, calendar_month["open_days"]) != -1) {
                return [true, "","Available"];
            } else {
                return [false,"","unAvailable"];
            }
        }

        function formatDate(day,format) {
            var day = day.split('-');
            switch (format) {
              case 'dd/mm/yyyy':
                day = day[2] + '/' + day[1] + '/' + day[0];
                break;
              case 'mm/dd/yyyy':
                day = day[1] + '/' + day[2] + '/' + day[0];
                break;
              case 'yyyy/mm/dd':
                day = day[0] + '/' + day[1] + '/' + day[2];
                break;
              default:
                day = day[2] + '/' + day[1] + '/' + day[0];
            }
            return day;
        }

        function formatTime(hhmm,format) {
            switch (format) {
              case 'hh:mm':
                time = hhmm;
                break;
              case 'hh:mm tt':
                time = formatAMPM(hhmm,format);
                break;
              case 'hh:mm TT':
                time = formatAMPM(hhmm,format);
                break;
              default:
                time = hhmm;
            }
            return time;
        }

        function formatAMPM(hhmm,format) {
            hhmmArray = hhmm.split(':');
            var hours = hhmmArray[0];
            var minutes = hhmmArray[1];
            if (format == 'hh:mm TT') {
                var ampm = hours >= 12 ? 'PM' : 'AM';
            } else {
                var ampm = hours >= 12 ? 'pm' : 'am';
            }
            hours = hours % 12;
            hours = hours ? hours : 12; // the hour '0' should be '12'
            hours = (hours < 10 && hours != 0) ? '0'+hours : hours;
            minutes = (minutes < 10 && minutes != 0) ? '0'+minutes : minutes;
            var strTime = hours + ':' + minutes + ampm;
            return strTime;
        }

        function waitForGetDays() {
            if (typeof calendar_month !== "undefined") {
                $('#rezque #date').datepicker({
                    dateFormat: calendar_month['date_format'].replace('yyyy', 'yy'),
                    minDate: 0,
                    maxDate: 60,
                    beforeShowDay: available,
                    onChangeMonthYear: function(year,month) {
                        getDays(year,month,calendar_id);
                    },
                    onSelect: function(dateText) {
                        var date = dateText.split('/');          
                        if (calendar_month['date_format'] == 'dd/mm/yyyy') {
                            var year = date[2],
                            month = date[1],
                            day = date[0];
                        } else if (calendar_month['date_format'] == 'mm/dd/yyyy') {
                            var year = date[2],
                            month = date[0],
                            day = date[1];
                        } else if (calendar_month['date_format'] == 'yyyy/mm/dd') {
                            var year = date[0],
                            month = date[1],
                            day = date[2];
                        }
                        getSlots(year,month,day,calendar_id);
                        $('#rezque input#day').val(dateText);
                        $('#rezque .fs-subtitle').html('Date: ' + dateText);
                		current_fs = $(this).parent();
                		current_fs.hide();
                		next_fs = $(this).parent().next();
						next_fs.fadeIn(500);
                    }
                });
                $('#rezque input#date_format').val(calendar_month['date_format_raw']);
                // Add conditions for location information

                if (calendar_month["location_office"].length > 0 ) {
                    if (calendar_month["location_home"] == true) {
                        $("#rezque .btn-group-vertical input[name=hof]").on('change', function () {
                            if($("#rezque #appointment_hof_home_visit").is(':checked')) {
                                $("#rezque #address").slideDown();
                                $("#rezque #address").attr("required", true);
                                $(this).parent().addClass('active');
                                $(this).parent().siblings().removeClass('active');
                            } else {
                                $("#rezque #address").slideUp();
                                $("#rezque #address").removeAttr('required');
                                $(this).parent().addClass('active');
                                $(this).parent().siblings().removeClass('active');
                            }
                        });
                    } else {
                        $("#rezque #location").remove();
                    }
                // In case office location is blank
                } else {
                    // But home location is checked
                    if ( calendar_month["location_home"] == true) {
                        $("#rezque #hof").remove();
                        $("#rezque #address").show();
                    }
                }

            } else {
                setTimeout(function(){
                    waitForGetDays();
                },250);
            }   
        }   
    });

});