<style>
    .left-menu{ list-style: decimal-leading-zero;}
</style>
<ul class="left-menu">
    <?php foreach ($indexlist as $row) { ?>
        <li>
            <label class="pointer">
                <a href="javascript:void(0)" onclick="getindexdata('<?php echo $row->index; ?>')"><?php echo $row->index; ?></a>
            </label>
        </li>
    <?php } ?>
</ul>

<script>

    function showtypelist($indexname) {
        //now create index
        $suburl = 'index=indexlist';
        $.ajax({
            "url": $baseurl,
            "method": "post",
            "data": {"action": $suburl, "indexname": $indexname},
            "success": function ($res) {

                //console.log($res);

                $res = JSON.parse($res);
                if ($res['status'] == 'fail') {
                    alert($res['message']);
                } else if ($res['status'] == 'success') {
                    $('#_response_').html($res['data']);
                    //$('#_jsleftside_').html($res['leftside']);
                    //alert($res['message']);
                }
            }
        });
    }

    function getindexdata($indexname) {
        $suburl = 'index=indexdata';
        $.ajax({
            "url": $baseurl,
            "method": "post",
            "data": {"action": $suburl, "indexname": $indexname},
            "success": function ($res) {
                $res = JSON.parse($res);
                if ($res['status'] == 'fail') {
                    alert($res['message']);
                } else if ($res['status'] == 'success') {
                    $('#_response_').html($res['data']);
                    $('#_jsmenu_').html($res['topmenu'])
                }
            }
        });
    }

</script>