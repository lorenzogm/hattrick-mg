<script type="text/javascript">
    $(document).ready(function(){
        $("#sync-step2").hide();
        $("#sync-step1").load("<?php echo site_url('sync/get_data');?>", function(response, status, xhr) {
            if (status == "success") {
                clearInterval(dummyLoad);

                $("#sync-step1").hide();
                $("#sync-step2").show();

                window.location = "squad";
            }
        });
        var bar = document.getElementById("bar"),
            fallback = document.getElementById("fallback"),
            loaded = 0;

        var load = function() {
            loaded += 10;
            bar.value = loaded;
            /* The below will be visible if the progress tag is not supported */
            $(fallback).empty().append("HTML5 progress tag not supported: " + loaded + "% loaded");

            if(loaded == 100)
                loaded = 0;
        };

        var dummyLoad = setInterval(function() {
            load();
        } ,500);
    });
</script>


<div id="sync-step1" class="well span3 offset4">
    <h2><?php echo lang('label:synchronizing');?></h2>

    <progress id="bar" value="0" max="100">
        <span id="fallback"></span>
    </progress>

    <h4><?php echo lang('label:sync_description');?></h4>
</div>

<div id="sync-step2" class="well span3 offset4">
    <h2><?php echo lang('label:synced');?></h2>
</div>