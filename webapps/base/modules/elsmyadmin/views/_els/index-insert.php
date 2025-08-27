<link href="//cdn.rawgit.com/Eonasdan/bootstrap-datetimepicker/e8bddc60e73c1ec2475f827be36e1957af72e2ea/build/css/bootstrap-datetimepicker.css" rel="stylesheet">
<script src="//cdnjs.cloudflare.com/ajax/libs/moment.js/2.9.0/moment-with-locales.js"></script>
<script src="//cdn.rawgit.com/Eonasdan/bootstrap-datetimepicker/e8bddc60e73c1ec2475f827be36e1957af72e2ea/src/js/bootstrap-datetimepicker.js"></script>

<h2 class="padding_left_15">Insert data to Index <span style="color:#c803f9;"><?php echo $indexname; ?></span></h2>
<br>
<div class="row">
    <div class="col-md-8">
        <div id="_jselsqueryplace_"></div>
        
        <form id="insertdata" name="insertdata" method="post">
            <table>
                <tr style="font-weight: bolder; background-color: #056eab; color: #FFF;">
                    <td width="250">Field Name</td>
                    <td>Controls</td>
                    <td>Data Type</td>
                </tr>
                <?php
                $i = 11;
                foreach ($indexstructureinfo as $fieldname => $prop) {
                    if ($fieldname == 'timestamp')
                        continue;
                    ?>
                    <tr>
                        <td><?php echo $fieldname; ?></td>
                        <td>
                            <!--add text area -->
                            <?php if (isset($prop->type) && $prop->type == "text") { ?>
                                <textarea name="<?php echo $fieldname; ?>" class="form-control"></textarea>
                            <?php } ?>
                            <!--add input text -->
                            <?php if (isset($prop->type) && $prop->type == "integer") { ?>
                                <input type="number" name="<?php echo $fieldname; ?>" class="form-control" />
                            <?php } ?>
                            <!--add input number -->
                            <?php if (isset($prop->type) && $prop->type == "date") { ?>
                                <input name="<?php echo $fieldname; ?>" type="text" class="datepicker"/>
                                <span class="input-group-addons"><span class="glyphicon glyphicon-calendar"></span>
                                <?php } ?>
                                <!--add for other type -->
                                <?php if (isset($prop->type) && ($prop->type != "date" && $prop->type != "text" && $prop->type != "integer")) { ?>
                                    <input type="text" name="<?php echo $fieldname; ?>" class="form-control" />
                                <?php } ?>
                        </td>
                        <td><?php
                            echo $prop->type;
                            if (isset($prop->format)) {
                                echo ', Format: ' . $prop->format;
                            };
                            ?></td>
                    </tr>
                <?php } ?>
                <tr>
                    <td></td>
                    <td></td>
                    <td><input type="submit" value="Submit"></td>
                </tr>
            </table>
        </form>
    </div>
</div>

<script type="text/javascript">
    

    $("form").on("submit", function (event) {
        event.preventDefault();
        var $indexname = '<?php echo $indexname; ?>';
        var $data = $(this).serializeArray();
        var $suburl = 'indexinsert=save';
        $.ajax({
            "url": $baseurl,
            "method": "post",
            "data": {"action": $suburl, "indexname": $indexname, "data": $data},
            "success": function ($res) {
                $res = JSON.parse($res);
                if ($res['status'] == 'fail') {
                    alert($res['message']);
                } else if ($res['status'] == 'success') {
                    $('#_jselsqueryplace_').html($res['data']);
                    console.log($res);
                }
            }
        });

    });

</script>
