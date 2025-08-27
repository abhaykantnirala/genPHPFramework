<style>
    fieldset.scheduler-border {
        border: 1px groove #ddd !important;
        padding: 0 1.4em 1.4em 1.4em !important;
        margin: 0 0 1.5em 0 !important;
        -webkit-box-shadow:  0px 0px 0px 0px #000;
        box-shadow:  0px 0px 0px 0px #000;
    }

    legend.scheduler-border {
        font-size: 1.2em !important;
        font-weight: bold !important;
        text-align: left !important;
        width:auto;
        padding:0 10px;
        border-bottom:none;
    }
</style>
<div class="row">
    <div class="col-md-3">


        <fieldset class="scheduler-border">
            <legend class="scheduler-border">Truncate <span style="color:#c803f9;"><?php echo $indexname; ?></span></legend>
            <div class="control-group">
                <br>
                <a href="javascript:void(0);" onclick="truncateindex('<?php echo $indexname; ?>')">Truncate index <span style="font-weight: bold;" title="Truncate index <?php echo $indexname; ?>"><?php echo $indexname; ?></span></a>
            </div>
        </fieldset>

    </div>
</div>

<script>
    function truncateindex($indexname) {
        $suburl = 'index=truncate';
        var $result = confirm('Do you really want to truncate ' + $indexname + ' index?');
        if ($result) {
            $.ajax({
                "url": $baseurl,
                "method": "post",
                "data": {"action": $suburl, "indexname": $indexname},
                "success": function ($res) {
                    $res = JSON.parse($res);
                    if ($res['status'] == 'fail') {
                        alert($res['message']);
                    } else if ($res['status'] == 'success') {
                        getindexdata($indexname);
                    }
                }
            })
        }
    }
</script>