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
    <h2 class="padding_left_15">Index structure of <span style="color:#c803f9;"><?php echo $indexname; ?></span></h2>
    <br>
    <?php if (count((array) $indexstructureinfo)) { ?>
        <div class="col-md-6">
            <table>
                <tr class="index-head">
                    <td>#</td>
                    <td>Name</td>
                    <td>Type</td>
                    <td>Default</td>
                    <td>Format</td>
                    <td>Action</td>
                </tr>
                <?php
                $i = 0;
                foreach ($indexstructureinfo as $name => $rows) {
                    ?>
                    <tr>
                        <td><?php echo ++$i; ?></td>
                        <td><?php echo $name; ?></td>
                        <td><?php echo $rows->type; ?></td>
                        <td><?php echo $rows->null_value??''; ?></td>
                        <td><?php echo $rows->format??''; ?></td>
                        <td>Delete</td>
                    </tr>

                <?php } ?>
            </table>
        </div>
    <?php } ?>
</div>
<hr>
<a href="javascript:void(0);">Create Structure</a>
<?php
$typeslist = array('text', 'string', 'array', 'binary', 'range', 'boolean', 'date', 'geo-point', 'geo-shape', 'ip', 'keyword', 'nested', 'numeric', 'integer', 'long', 'float', 'object', 'token', 'join', 'double');
sort($typeslist);
?>
<div class="row">
    <div class="col-md-4" id="_jsstructure_">
        <table>
            <tr>
                <td>Name</td>
                <td>Type</td>
                <td>Default</td>
            </tr>
            <tr class="_jsstructuredesign_ _jsstructurevalues_">
                <td><input type="text" class="_jsname_" value=""></td>
                <td>
                    <select class="_jstype_">
                        <?php foreach ($typeslist as $row) { ?>
                            <option value="<?php echo $row; ?>" <?php if ($row == 'integer') { ?> selected="selected"<?php } ?>><?php echo $row; ?></option>
                        <?php } ?>
                    </select>
                </td>
                <td><input type="text" class="_jsdefault_" value=""></td>
            </tr>
        </table>
    </div>
</div>
<br>
<div class="row">
    <div class="col-md-4">
        <div class="row">
            <div class="col-md-6"><a href="javascript:void(0);" onclick="addmorefields('<?php echo $indexname; ?>')">Add more</a></div>
            <div class="col-md-6"><input type="button" value="Save" name="btnsave" onclick="savestructure('<?php echo $indexname; ?>')" style="float:right;"></div>
        </div>
    </div>
</div>

<hr>
<br><br><br><br><br><br>

<script>
    function addmorefields($indexname) {
        var $structure = '<tr class="_jsstructurevalues_">' + $('._jsstructuredesign_').html() + '</tr>';
        $('#_jsstructure_ table').append($structure);
        //createindexstructure($indexname);
    }
    function savestructure($indexname) {
        var $info = [];
        $('._jsstructurevalues_').each(function (i) {
            var $name = $(this).find('._jsname_').val().trim();
            var $type = $(this).find('._jstype_').val();
            var $default = $(this).find('._jsdefault_').val().trim();
            if ($name.trim() != '') {
                $info.push({"name": $name, "type": $type, "default": $default});
            }
        });
        if ($info.length == 0) {
            alert('At least one field name required');
        } else {
            //now send data to server to add field
            $info = JSON.stringify($info);
            $suburl = 'indexstructure=save';
            $.ajax({
                "url": $baseurl,
                "method": "post",
                "data": {"action": $suburl, "indexname": $indexname, "data": $info},
                "success": function ($res) {
                    $res = JSON.parse($res);
                    //console.log($res);
                    if ($res['status'] == 'fail') {
                        alert($res['message']);
                    } else if ($res['status'] == 'success') {
                        alert($res['message']);
                        createindexstructure($indexname);
                    }
                }
            });
        }
    }
</script>
