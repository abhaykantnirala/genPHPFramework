<?php $formatter = new NumberFormatter('en_IN', NumberFormatter::CURRENCY); ?>
<style>
    .table-ts th,td{
        margin: 5px;
        padding: 5px;
    }
</style>
<table>
    <tr>
        <?php
        $is_record = false;
        foreach ($user_emi_data as $plan_name => $row) {
            $is_record = true;
            ?>
            <td><h3 style="text-align: center; color: #0036ff;"><?php echo $plan_name . '(' . $formatter->formatCurrency($row[0]->plan_amount, 'INR') . ')'; ?></h3></td>
        <?php } ?>
        <?php if (!$is_record) { ?>
            <td><h3 style="text-align: center; color: #0036ff;">No Record Found</h3></td>
        <?php } ?>
    </tr>
    <tr>
<?php foreach ($user_emi_data as $plan_name => $rows) { ?>
            <td style="vertical-align:top; padding:0 15px">
                <table border="1" cellpadding="5" class="table-ts">
                    <thead>
                    <th>EMI No</th>
                    <th>EMI Received</th>
                    <th>Receiving Method</th>
                    <th>EMI Date</th>
                    </thead>
                    <tbody>
                        <?php
                        $cnt = 0;
                        foreach ($rows as $row) {
                            ?>
                            <tr>
                                <td><?php echo ++$cnt; ?></td>
                                <td><?php echo $formatter->formatCurrency($row->emi_amount, 'INR'); ?></td>
                                <td><?php echo $row->emi_received_method; ?></td>
                                <td><?php echo date('d M, y', strtotime($row->emi_date)); ?></td>
                            </tr>
    <?php } ?>
                    </tbody>
                </table>
            </td>
<?php } ?>
    </tr>
</table>