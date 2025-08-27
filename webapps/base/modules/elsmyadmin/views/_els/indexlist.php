<style>
    .index-head{
        font-size: 16px;
        color : #0377d0;
        background-color: #ccc;
    }
    .padding_left_15{padding-left: 15px;}
    ._tablewidthindex{width:400px !important;}
</style>
<div class="row">
    <h2 class="padding_left_15">Indexes</h2>
    <div class="padding_left_15">
        <a href="javascript:void(0)">Create index</a>
        <form method="post" id="index_form">
            <table class="_tablewidthindex">
                <tr>
                    <td>Index Name: </td>
                    <td><input type="text" value="" name="indexname" id="_jsindexname" required></td>
                </tr>
                <tr>
                    <td>Number of Shards: </td>
                    <td><input type="number" min="1" value="1" name="numberofshards" id="_jsshards" required></td>
                </tr>
                <tr>
                    <td>Number of Replicas: </td>
                    <td><input type="number" min="1" value="1" name="numberofreplicas" id="_jsreplicas" required></td>
                </tr>
                <tr>
                    <td></td>
                    <td><input type="button" onclick="createindex()" value="Create"></td>
                </tr>
            </table>
        </form>
    </div>
    <br><br>
    <div class="col-md-4">
        <table>
            <tr class="index-head">
                <td></td>
                <td>Index</td>
                <td>Size</td>
                <td>Health</td>
                <td></td>
            </tr>
            <?php
            foreach ($indexlist as $row) {
                $row = (array) $row;
                ?>
                <tr>
                    <td><input type="checkbox" value="<?php echo $row['index']; ?>"</td>
                    <td><a href="javascript:void(0)" onclick="getindexdata('<?php echo $row['index']; ?>')"><?php echo $row['index']; ?></a></td>
                    <td><?php echo $row['store.size']; ?></td>
                    <td><div style="height: 15px; width: 90%; background-color: <?php echo $row['health']; ?>"></div></td>
                    <td><a href="javascript:void(0);" onclick="deleteindex('<?php echo $row['index']; ?>');">Drop</a></td>
                </tr>
            <?php } ?>
            <?php if ($total = count($indexlist)) { ?>
                <tr>
                    <td></td>
                    <td style="font-weight: bold !important;">Total: <?php echo $total; ?></td>
                    <td></td>
                    <td></td>
                    <td></td>
                </tr>
            <?php } ?>
        </table>
    </div>
</div>
<br><br>
<hr>
<script>
    function deleteindex($indexname) {
        var $result = confirm('You are about to DELETE a complete index!\nDo you really want to execute "DELETE ' + $indexname + '"?');
        if ($result) {
            $suburl = 'index=delete';
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
                        $('#_jsleftside_').html($res['leftside']);
                        alert($res['message']);
                    }
                }
            })
        }
    }

    function createindex() {
        var $form = $("#index_form");
        var unindexed_array = $form.serializeArray();
        var $data = {};

        $.map(unindexed_array, function (n, i) {
            $data[n['name']] = n['value'];
        });

        $data['action'] = 'index-create';

        if ($data['indexname'].length < 2) {
            alert('Index name required');
            $('#_jsindexname').focus();
            return false;
        }

        //now create index
        $.ajax({
            "url": $baseurl,
            "method": "post",
            "data": $data,
            "success": function ($res) {
                $res = JSON.parse($res);
                if ($res['status'] == 'fail') {
                    alert($res['message']);
                } else if ($res['status'] == 'success') {
                    //reset form data
                    $(':input', '#index_form')
                            .not(':button, :submit, :reset, :hidden')
                            .val('')
                            .prop('checked', false)
                            .prop('selected', false);

                    $('#_response_').html($res['data']);
                    $('#_jsleftside_').html($res['leftside']);
                    alert($res['message']);
                }
            }
        })
    }
</script>

