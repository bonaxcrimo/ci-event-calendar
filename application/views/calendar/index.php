<!DOCTYPE html>
<html>
    <head>
    <title>Calendar</title>
        <meta charset='utf-8' />
        <meta content="width=device-width, initial-scale=1, maximum-scale=3, user-scalable=yes,minimum-scale=1" name="viewport">
        <link href="<?php echo base_url();?>libraries/adminlte/bower_components/bootstrap/dist/css/bootstrap.min.css" rel="stylesheet">
        <link href='<?php echo base_url();?>libraries/fullcalendar/fullcalendar.min.css' rel='stylesheet' />
        <link href="<?php echo base_url();?>libraries/fullcalendar/bootstrapValidator.min.css" rel="stylesheet" />
        <link href="<?php echo base_url();?>libraries/fullcalendar/bootstrap-colorpicker.min.css" rel="stylesheet" />
        <link href="<?php echo base_url();?>libraries/fullcalendar/bootstrap-colorpicker.min.css" rel="stylesheet" />
        <link href="<?php echo base_url();?>libraries/fullcalendar/bootstrap-datetimepicker.css" rel="stylesheet" />
        <script src='<?php echo base_url();?>libraries/fullcalendar/moment.min.js'></script>
        <script src="<?php echo base_url();?>libraries/fullcalendar/jquery.min.js"></script>
        <script src="<?php echo base_url();?>libraries/fullcalendar/bootstrap.min.js"></script>
        <script src="<?php echo base_url();?>libraries/fullcalendar/bootstrapValidator.min.js"></script>
        <script src="<?php echo base_url();?>libraries/fullcalendar/fullcalendar.min.js"></script>
        <script src='<?php echo base_url();?>libraries/fullcalendar/bootstrap-colorpicker.min.js'></script>
        <script src='<?php echo base_url();?>libraries/fullcalendar/bootstrap-datetimepicker.js'></script>
        <script src='<?php echo base_url();?>libraries/fullcalendar/main_c.js'></script>
        <link rel="stylesheet" href="<?= base_url() ?>libraries/bootstrap-fs-modal/fs-modal.min.css">
        <script src="<?= base_url() ?>libraries/bootstrap-fs-modal/fs-modal.min.js"></script>
    <style>
    .fc-sun {
        color:#black;
        border-color: black;
        background-color: #fcf8e3;
    }
    </style>
    </head>
    <body>
<div class="">
        <!-- Notification -->
        <div class="alert"></div>
        <div id='calendar'></div>
</div>
    <div class="modal fade modal-fullscreen">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">&times;</span><span class="sr-only">Close</span></button>
                    <h4 class="modal-title"></h4>
                </div>
                <div class="modal-body">
                    <div class="error"></div>
                    <form class="form-vertical" id="crud-form">

                        <div class="form-group">
                            <label class="control-label" for="title">Title</label>
                                <input id="title" name="title" type="text" class="form-control input-md" />
                        </div>
                        <div class="form-group">
                            <label class="control-label" for="description">Description</label>
                            <textarea class="form-control" id="description" name="description"></textarea>
                        </div>
                        <div class="form-group">
                            <label class="control-label" for="color">Color</label>
                            <div class="row">
                                <div class="col-md-2">
                                    <input id="color" name="color" type="color" class="form-control input-md" readonly="readonly" />
                                </div>
                                <div class="col-md-4">
                                    <span class="help-block">Click to pick a color</span>
                                </div>

                            </div>

                        </div>
                        <div class="form-group">
                            <div class="row">
                                <div class="col-md-6">
                                    <label class="control-label" for="title">Start</label>
                                    <div class='input-group date' id='datetimepicker1'>
                                        <input type='text' id="start" class="form-control" />
                                        <span class="input-group-addon">
                                            <span class="glyphicon glyphicon-calendar"></span>
                                        </span>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                 <label class="control-label" for="title">End</label>
                                    <div class='input-group date' id='datetimepicker2'>
                                        <input type='text' id="end" class="form-control" />
                                        <span class="input-group-addon">
                                            <span class="glyphicon glyphicon-calendar"></span>
                                        </span>
                                    </div>
                                </div>
                            </div>

                        </div>
                    </form>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-default" data-dismiss="modal">Cancel</button>
                </div>
            </div>
        </div>
    </div>
<script type="text/javascript">
    $(function () {
        hide_notify();
        $('#calendar').fullCalendar({
        customButtons: {
            backButton: {
              text:'Logout',
              click: function() {
                location.replace(base_url+'login/logout');
              }
            }
          },
        height:$(window).height(),
        header: {
            left: 'backButton ,today ',
            center: 'title',
            right: 'prev,next'
        },viewRender: function(view) {
                var title = view.title;
                var text = "Test ";
                $(".fc-center").html("<h2 style='color:red'>"+(text + title)+"</h2>");
              },
        // Get all events stored in database
        eventLimit: true, // allow "more" link when too many events
        events: base_url+'extension/getEvents',
        selectable: true,
        selectHelper: true,
        displayEventTime :true,
        editable: <?= hasPermission('calendar','updateEvent')?"true":"false" ?>, // Make the event resizable true
            select: function(start, end) {
            modal({
                // Available buttons when adding
                buttons: {
                    add: {
                        id: 'add-event', // Buttons id
                        css: 'btn-success', // Buttons class
                        label: 'Add' // Buttons label
                    }
                },
                title: 'Add Event' // Modal title
            });
            $('#start').val(moment(start).format('YYYY-MM-DD HH:mm:ss'));
            $('#end').val(moment(end).format('YYYY-MM-DD HH:mm:ss'));
            },

         eventDrop: function(event, delta, revertFunc,start,end) {
            // alert("you don't have access")
            // return false;
            start = event.start.format('YYYY-MM-DD HH:mm:ss');
            if(event.end){
                end = event.end.format('YYYY-MM-DD HH:mm:ss');
            }else{
                end = start;
            }

           $.post(base_url+'calendar/dragUpdateEvent',{
                id:event.id,
                start : start,
                end :end
            }, function(result){
                $('.alert').addClass('alert-success').text('Event updated successfuly');
                hide_notify();


            });



          },
          eventResize: function(event,dayDelta,minuteDelta,revertFunc) {

                start = event.start.format('YYYY-MM-DD HH:mm:ss');
            if(event.end){
                end = event.end.format('YYYY-MM-DD HH:mm:ss');
            }else{
                end = start;
            }

               $.post(base_url+'calendar/dragUpdateEvent',{
                id:event.id,
                start : start,
                end :end
            }, function(result){
                $('.alert').addClass('alert-success').text('Event updated successfuly');
                hide_notify();

            });
            },

        // Event Mouseover
        eventMouseover: function(calEvent, jsEvent, view){

            // var tooltip = '<div class="event-tooltip">' + calEvent.description + '</div>';
            // $("body").append(tooltip);

            // $(this).mouseover(function(e) {
            //     $(this).css('z-index', 10000);
            //     $('.event-tooltip').fadeIn('500');
            //     $('.event-tooltip').fadeTo('10', 1.9);
            // }).mousemove(function(e) {
            //     $('.event-tooltip').css('top', e.pageY + 10);
            //     $('.event-tooltip').css('left', e.pageX + 20);
            // });
        },
        eventMouseout: function(calEvent, jsEvent) {
            $(this).css('z-index', 8);
            $('.event-tooltip').remove();
        },
        // Handle Existing Event Click
        eventClick: function(calEvent, jsEvent, view) {
            // Set currentEvent variable according to the event clicked in the calendar
            currentEvent = calEvent;
            // Open modal to edit or delete event
            modal({
                // Available buttons when editing
                buttons: {
                    delete: {
                        id: 'delete-event',
                        css: 'btn-danger',
                        label: 'Delete',
                        dom:'<?= hasPermission("calendar","deleteEvent")?"":"disabled" ?>'
                    },
                    update: {
                        id: 'update-event',
                        css: 'btn-success',
                        label: 'Update',
                        dom:'<?= hasPermission("calendar","updateEvent")?"":"disabled" ?>'
                    }
                },
                title: 'Edit Event "' + calEvent.title + '"',
                event: calEvent
            });
        },
        eventAfterAllRender :function(view){
            $(".fc-backButton-button").css('background','yellow');
        }

    });

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
        $('#datetimepicker1,#datetimepicker2').datetimepicker({
            format:'YYYY-MM-DD HH:mm:ss'
        })
        $('#datetimepicker1').datetimepicker().on('dp.change',function(e){
            var incrementDay = moment(new Date(e.date));
            incrementDay.add(1, 'days');
            $('#datetimepicker2').data('DateTimePicker').minDate(incrementDay);
            $(this).data("DateTimePicker").hide();
        });
        $('#datetimepicker2').datetimepicker().on('dp.change', function (e) {
            var decrementDay = moment(new Date(e.date));
            decrementDay.subtract(1, 'days');
            $('#datetimepicker1').data('DateTimePicker').maxDate(decrementDay);
             $(this).data("DateTimePicker").hide();
        });
    });
</script>
    </body>
</html>