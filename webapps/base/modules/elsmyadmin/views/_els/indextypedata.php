<?php
$fieldslist = $indexdata['fields'] ?? array();
$fieldsdata = $indexdata['data'] ?? array();
?>
<style>
    .index-head{
        font-size: 16px;
        color : #0377d0;
        background-color: #ccc;
    }
    .padding_left_15{padding-left: 15px;}
    ._tablewidthindex{width:400px !important;}
</style>
<div class="row" style="margin-left:-3px;">
    <h2 class="padding_left_15">Index data of <span style="color:#c803f9;"><?php echo $indexname; ?></span></h2>
    <table>
        <tr>
            <td style="width:70px;">Where</td>
            <td><input type="text" value="" style="width: 90%; border-width: thin; height: 40px;" ></td>
            <td><input type="button" style="margin-left:40px;" onclick="showfulltext()" value="Show Full Text"></td>
        </tr>
        <tr>
            <td style="width:50px;">Limit</td>
            <td><input type="number" value="50" min="1" style="border-width: thin;" ></td>
            <td></td>
        </tr>
        <tr>
            <td style="width:50px;">Order By</td>
            <td>
                <select>
                    <option value="">Select Option</option>
                    <?php foreach ($fieldslist as $rows) { ?>
                        <option value="<?php echo $rows; ?>"><?php echo $rows; ?></option>
                    <?php } ?>
                </select>
            </td>
            <td></td>
        </tr>
        <tr>
            <td></td>
            <td><input type="button" onclick="showfilterdata()" value="Search"></td>
            <td></td>
        </tr>
    </table>

    <hr>
    <?php
    $fieldname = $this->session->getdata('fieldname');
    $sortby = $this->session->getdata('sortby');
    if (count($fieldsdata)) {
        ?>
        <div class="col-md-4">
            <table>
                <tr class="index-head">
                    <td style="min-width: 150px;"></td>
                    <td>
                        <a href="javascript:void(0);" onclick="sort('<?php echo $indexname; ?>', '_id', '<?php echo $sortby; ?>')">_id</a>
                        <?php if ($fieldname && $fieldname == '_id') { ?>
                            <span style="color:blue;"><?php echo $sortby; ?></span>
                        <?php } ?>
                    </td>
                    <td>
                        <a href="javascript:void(0);" onclick="sort('<?php echo $indexname; ?>', '_score', '<?php echo $sortby; ?>')">_score</a>
                        <?php if ($fieldname && $fieldname == '_score') { ?>
                            <span style="color:blue;"><?php echo $sortby; ?></span>
                        <?php } ?>
                    </td>
                    <?php foreach ($fieldslist as $rows) { ?>
                        <td style="min-width: 150px;">
                            <a href="javascript:void(0);" onclick="sort('<?php echo $indexname; ?>', '<?php echo $rows; ?>', '<?php echo $sortby; ?>')"><?php echo strip_tags($rows); ?></a>
                            <?php if ($fieldname && $fieldname == $rows) { ?>
                                <span style="color:blue;"><?php echo $sortby; ?></span>
                            <?php } ?>
                        </td>
                    <?php } ?>
                </tr>
                <?php foreach ($fieldsdata as $rows) { ?>
                    <tr>
                        <td valign="top"><a href="javascript:void(0)" onclick="deleteindextypedoc('<?php echo $rows->_id; ?>', '<?php echo $indexname; ?>')">Delete</a></td>
                        <td valign="top"><?php echo $rows->_id; ?></td>
                        <td valign="top"><?php echo $rows->_score; ?></td>
                        <?php foreach ($fieldslist as $fieldname) { ?>
                            <td valign="top" style="max-width: 500px; word-wrap: break-word;"><?php
                                if (isset($rows->{$fieldname})) {
                                    echo strip_tags(substr($rows->{$fieldname}, 0, 500));
                                }
                                ?>
                                <div class="_full_value_" style="display: none; width: 100%; height: 100px;">
                                    <br>
                                    <br>
                                    <textarea style="width: 100%; height: 100px; background-color: #e4e3e3;">
                                        <?php
                                        if (isset($rows->{$fieldname})) {
                                            echo strip_tags($rows->{$fieldname});
                                        }
                                        ?>
                                    </textarea>
                                </div>
                            </td>
                        <?php } ?>
                    </tr>
                <?php } ?>
            </table>
        </div>
    <?php } else { ?>
        <h2>No record found</h2>
    <?php } ?>
</div>
<script>
    function sort($indexname, $fieldname, $sortby) {
        $suburl = 'index=sort-view';
        $.ajax({
            "url": $baseurl,
            "method": "post",
            "data": {"action": $suburl, "indexname": $indexname, "fieldname": $fieldname, "sortby": $sortby},
            "success": function ($res) {
                $res = JSON.parse($res);
                if ($res['status'] == 'fail') {
                    alert($res['message']);
                } else if ($res['status'] == 'success') {
                    $('#_response_').html($res['data']);
                    $('#_jsmenu_').html($res['topmenu'])
                }
            }
        })
    }
    function showfulltext() {
        $('._full_value_').show();
    }
</script>