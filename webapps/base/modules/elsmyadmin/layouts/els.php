<!DOCTYPE html>
<html>
    <head>
        <title>elsMyAdmin</title>
        <meta http-equiv="cache-control" content="max-age=0" />
        <meta http-equiv="cache-control" content="no-cache" />
        <meta http-equiv="expires" content="Tue, 01 Jan 1980 1:00:00 GMT" />
        <meta http-equiv="pragma" content="no-cache" />
        <link href="<?php echo $this->helper->url->baseurl(SCRIPTPATH . '/js/bootstrap/dist/css/bootstrap.css'); ?>" rel="stylesheet">
        <link href="<?php echo $this->helper->url->baseurl(SCRIPTPATH . '/css/style.css'); ?>" rel="stylesheet">
        <script type="text/javascript" src="<?php echo $this->helper->url->baseurl(SCRIPTPATH . 'js/jquery.js'); ?>"></script>
        <script type="text/javascript" src="<?php echo $this->helper->url->baseurl(SCRIPTPATH . 'js/json-viewer.js'); ?>"></script>
        <script src="//cdnjs.cloudflare.com/ajax/libs/jquery/3.2.1/jquery.min.js"></script>
        <script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/js/bootstrap.min.js"></script>
    </head>
    <style>
        #_response_{overflow-x: auto; padding-bottom: 20px; max-height: 700px; padding-right: 30px;}
        .leftside{
            padding:5px; background-color: #f3f3f3; height: 800px;
        }
        .server-head{
            padding:5px; background-color:#888; text-shadow: 0 1px 0 #888; color:#FFFFFF;
        }
        /*TABS CSS*/
        body {
            font-family: 'Open Sans', sans-serif;
            font-weight: 300;
        }
        .tabs {
            margin: 0 auto;
        }
        #tab-button {
            display: table;
            table-layout: fixed;
            width: auto;
            margin: 0;
            padding: 0;
            list-style: none;
        }
        #tab-button li {
            display: table-cell;

        }
        #tab-button li a {
            display: block;
            padding: .5em;
            background: #eee;
            border: 1px solid #ddd;
            text-align: center;
            color: #000;
            text-decoration: none;
            padding: 7px 20px;
        }
        #tab-button li:not(:first-child) a {
            border-left: none;
        }

        #tab-button .is-active a {
            border-bottom-color: transparent;
            background: #fff;
            padding: 7px 20px;
        }
        .tab-contents {
            padding: .5em 2em 1em;
            border: 1px solid #ddd;
        }



        .tab-button-outer {
            display: none;
        }
        .tab-contents {
            margin-top: 20px;
        }
        @media screen and (min-width: 768px) {
            .tab-button-outer {
                position: relative;
                z-index: 2;
                display: block;
            }
            .tab-select-outer {
                display: none;
            }
            .tab-contents {
                position: relative;
                top: -1px;
                margin-top: 0;
            }
        }
    </style>
    <body class="container-fluid beautify" style="background-color:#FFFFFF;">
        <div class="row">
            <div class="col-sm-12 col-md-12">
                <div class="row">
                    <div class="col-sm-2 col-md-2">
                        <?php echo $_leftside_; ?>
                    </div>
                    <div class="col-sm-10 col-md-10">
                        <div class="server-head">Server: localhost</div>
                        <div class="row">
                            <div class="col-md-12">
                                <div class="tabs">
                                    <div class="tab-button-outer" id="_jsmenu_">
                                        <?php echo $_menu_; ?>
                                    </div>
                                </div>
                                <hr>
                                <div id="_response_">
                                    <?php echo $_body_; ?>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </body>
</html>
<script>
    var $baseurl = '<?php echo $this->helper->url->baseurl('_fetchdata'); ?>';
    var $suburl = '';

    //clear browser url
    window.history.pushState({page: "another"}, "another page", '<?php echo $this->helper->url->baseurl('_els'); ?>');

    function fetchdata($action) {
        switch ($action) {
            case 'getindexlist':
                getindexlist();
                break;
        }
    }

    function getindexlist() {
        $suburl = 'index=list';
        $.ajax({
            "url": $baseurl,
            "method": "post",
            "data": {"action": $suburl},
            "success": function ($res) {
                $('#indextab').addClass('is-active');
                window.history.pushState({page: "another"}, "another page", "?" + $suburl);
                $('#_response_').html($res);
            }
        })
    }

    function deleteindextypedoc($rowid, $indexname) {
        var $result = confirm('Do you really want to execute selected row?');
        if ($result) {
            $suburl = 'indextypedoc=delete';
            $.ajax({
                "url": $baseurl,
                "method": "post",
                "data": {"action": $suburl, "indexname": $indexname, "_id": $rowid},
                "success": function ($res) {
                    $res = JSON.parse($res);
                    alert($res['message']);
                    getindexdata($indexname);
                }
            })
        }
    }

    function createindexstructure($indexname) {
        $('#_jsmenu_ li').removeClass('is-active');
        $('#_jsstructure_').addClass('is-active');
        $suburl = 'indexstructure=view';
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
                }
            }
        })
    }
    
    function operationpage($indexname) {
        $('#_jsmenu_ li').removeClass('is-active');
        $('#_jsoperation_').addClass('is-active');
        $suburl = 'indexoperation=view';
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
                }
            }
        })
    }
    
    function insertintoindex($indexname) {
        $('#_jsmenu_ li').removeClass('is-active');
        $('#_jsinsertintoindex_').addClass('is-active');
        $suburl = 'indexinsert=view';
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
                }
            }
        })
    }

</script>
