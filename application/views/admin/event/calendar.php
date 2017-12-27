<script src="<?php echo base_url('asset/admin/plugin/fullcalendar/fullcalendar.min.js'); ?>"></script> 
<link rel="stylesheet" href="<?php echo base_url('asset/admin/plugin/fullcalendar/fullcalendar.min.css'); ?>" type="text/css" media="screen" />  

<div class="row">
    <div class="col-xs-12"> 
        <div class="box box-info">
            <div class="box-header">
                <h3 class="box-title"><?php echo isset($pageHeading) ? $pageHeading : ''; ?></h3>
            </div>
            <!-- /.box-header -->
            <div class="box-body"> 
                <div id='calendar' style="float: left;width: 100%;"></div> 
            </div>
            <!-- /.panel-body -->
        </div>
        <!-- /.panel -->
    </div>
    <!-- /.col-lg-12 -->
</div>

<div id="_aIuI_Man" style="display: none;">
    <ul class="_dsKi">         
        <li><a href="<?php echo base_url("admin/events/manage") ?>" class="add-event">Add New Event</a></li>
    </ul>
</div> 

<script>
    $(document).ready(function () {
        $('#calendar').fullCalendar({
            header: {
                left: 'prev,next today',
                right: 'prevYear,nextYear',
                center: 'title'
            },
            loading: function (bool) {
                if (bool)
                    $('#loading_calander').show();
                else
                    $('#loading_calander').hide();
            },
            defaultDate: '<?php echo isset($_GET['start_date']) ? date('Y-m-d', strtotime($_GET['start_date'])) : date('Y-m-d'); ?>',
            editable: false,
            eventLimit: true,
            eventSources: [
                {
                    url: '<?php echo site_url('admin/events/calendarEvent'); ?>',
                    color: '#00E6EC',
                    success: function (res) {
                        $('.total_eventR').html(res.length);
                    }
                }
            ],
            viewRender: (function (view) {
                var start_date = view.intervalStart.format();
                var end_date = view.intervalEnd.format();
                window.history.pushState('', '', '?start_date=' + start_date + '&end_date=' + end_date);
                //$(".fc-day-number").append('<a href="#" class="fa fa-plus-circle add-cal-event"></a>');
            }),
            cache: true
        });


        $(document).on('click', "a.add-cal-event", function (e) {
            e.preventDefault();
            var dateCal = $(this).parent('td').data('date');
            $("#_aIuI_Man ul._dsKi > li > a").each(function (i, o) {
                $(o).data("content", JSON.stringify($(e.target).data('content')));
                var prevLink = $(this).attr('href');
                var uristring = updateQueryStringParameter(prevLink, 'date', dateCal);
                $(this).attr('href', uristring)
            });
            var mLeft = $(this).offset().left + ($(this).outerWidth()), mTop = $(this).offset().top;
            $("#_aIuI_Man").css({"left": mLeft, "top": mTop}).show();
        });
        $('body').not("a.add-cal-event").on('click', function (e) {
            $("#_aIuI_Man").hide();
        });

    });
    function updateQueryStringParameter(uri, key, value) {
        var re = new RegExp("([?&])" + key + "=.*?(&|$)", "i");
        var separator = uri.indexOf('?') !== -1 ? "&" : "?";
        if (uri.match(re)) {
            return uri.replace(re, '$1' + key + "=" + value + '$2');
        }
        else {
            return uri + separator + key + "=" + value;
        }
    }
</script> 

