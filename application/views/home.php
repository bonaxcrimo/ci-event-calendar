<!DOCTYPE html>
<html>
    <head>
    <title>Event Calendar</title>
        <meta charset='utf-8' />
        <meta content="width=device-width, initial-scale=1, maximum-scale=3, user-scalable=yes,minimum-scale=1" name="viewport">
        <link href="<?php echo base_url();?>libraries/fullcalendar/bootstrap.min.css" rel="stylesheet">
        <link href='<?php echo base_url();?>libraries/fullcalendar/fullcalendar.min.css' rel='stylesheet' />
        <link href="<?php echo base_url();?>libraries/fullcalendar/bootstrapValidator.min.css" rel="stylesheet" />
        <link href="<?php echo base_url();?>libraries/fullcalendar/bootstrap-colorpicker.min.css" rel="stylesheet" />


        <script src='<?php echo base_url();?>libraries/fullcalendar/moment.min.js'></script>
        <script src="<?php echo base_url();?>libraries/fullcalendar/jquery.min.js"></script>
        <script src="<?php echo base_url();?>libraries/fullcalendar/bootstrap.min.js"></script>
        <script src="<?php echo base_url();?>libraries/fullcalendar/bootstrapValidator.min.js"></script>
        <script src="<?php echo base_url();?>libraries/fullcalendar/fullcalendar.min.js"></script>
        <script src='<?php echo base_url();?>libraries/fullcalendar/bootstrap-colorpicker.min.js'></script>


        <script src='<?php echo base_url();?>libraries/fullcalendar/main_r.js'></script>
        <style>
            .fc-sun {
                color:red;
                border-color: black;
                background-color: #fcf8e3;
            }
            </style>
    </head>
    <body>
      <div id='calendar'></div>
      <script>
        var username = "<?= $username ?>";
        $(function(){
            // var re = new RegExp(/^.*\//);
            // var base_url=re.exec(window.location.href); // Here i define the base_url
            var base_url = window.location.origin + '/' + window.location.pathname.split ('/') [1] + '/';
            // Fullcalendar
            var centerTitle= 'title';

            var $calendar=$('#calendar');
            $calendar.fullCalendar({
                customButtons: {
                    loginButton: {
                      text:username!=''?'Dashboard':'Login',
                      click: function() {
                        location.replace(base_url+'login');
                      }
                    }
                  },
                height:$(window).height(),
                header: {
                    left: 'loginButton ,today ',
                    center: 'title',
                    right: 'prev,next'
                },
              viewRender: function(view) {
                var title = view.title;
                var text = "Test ";
                $(".fc-center").html("<h2 >"+(title)+"</h2>");
                $(".fc-right").append(`<select class="select_view fc-state-default fc-corner-right" style='padding:4px'>
                    <option value="month">Basic Month</option>
                    <option value="listMonth">List Month</option>
                    <option value="listWeek">List listWeek</option>
                    <option value="listDay">List Day</option>
                    <option value="agendaWeek">Agenda Week</option>
                    <option value="agendaDay">Agenda Day</option>
                    <option value="basicDay">Basic Day</option>
                    <option value="basicWeek">Basic Week</option>
                    </select>`);

               $(".select_view").on("change", function(event) {
                   $('#calendar').fullCalendar('changeView',this.value);
               });
              },
                // Get all events stored in database
                eventLimit: true, // allow "more" link when too many events
                events: base_url+'extension/getEvents',
                selectable: true,
                selectHelper: true,
                eventAfterAllRender :function(view){
                    // $(".fc-loginButton-button").css('background','yellow');
                },
                eventRender:function(eventObj,$el){
                    if( /Android|webOS|iPhone|iPad|iPod|BlackBerry|IEMobile|Opera Mini/i.test(navigator.userAgent) ) {

                    //   $(".fc-center h2").replaceWith(function () {
                    //     return "<h4 style='"+$(".fc-center h2").attr('style')+"'>" + $(this).html() + "</h4>";
                    // });

                    }
                    $el.popover({
                        title: eventObj.title,
                        content: eventObj.description,
                        trigger: 'hover',
                        placement: 'top',
                        container: 'body'
                    })

                }
            });



        });
      </script>
    </body>
</html>