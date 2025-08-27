<link href="<?php echo $this->helper->url->baseurl('https://cdn.datatables.net/1.13.7/css/jquery.dataTables.min.css'); ?>" rel="stylesheet" />
<script src="<?php echo $this->helper->url->baseurl('https://code.jquery.com/jquery-3.7.0.js'); ?>"></script>
<script src="<?php echo $this->helper->url->baseurl('https://cdn.datatables.net/1.13.7/js/jquery.dataTables.min.js'); ?>"></script>
<div class="row">
    <div class="col-12">
        <div class="card card-default">

            <div class="card-body">
                <table id="example" class="display" style="width:100%">
                    <thead>
                        <tr>
                            <th>First name</th>
                            <th>Last name</th>
                            <th>Position</th>
                            <th>Office</th>
                            <th>Start date</th>
                            <th>Salary</th>
                        </tr>
                    </thead>
                    <tfoot>
                        <tr>
                            <th>First name</th>
                            <th>Last name</th>
                            <th>Position</th>
                            <th>Office</th>
                            <th>Start date</th>
                            <th>Salary</th>
                        </tr>
                    </tfoot>
                </table>
            </div>
        </div>
    </div>
</div>

<script>
new DataTable('#example', {
    ajax: '<?php echo $this->helper->url->baseurl('get-customer-contact-request');?>',
    processing: true,
    serverSide: true
});
</script>