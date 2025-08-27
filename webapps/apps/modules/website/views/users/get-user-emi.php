<?php $formatter = new NumberFormatter('en_IN', NumberFormatter::CURRENCY); ?>
<table class="emi-details">
    <tr>
        <?php
        $is_record = false;
        $is_emi_completed = false;
        foreach ($user_emi_data as $plan_name => $row) {
            $is_record = true;
            $is_emi_completed = $row[0]->emi_completed;
            ?>
            <td><h3 style="text-align: center; color: #0036ff;"><?php echo $plan_name . '(' . $formatter->formatCurrency($row[0]->plan_amount, 'INR') . ')'; ?></h3></td>
        <?php } ?>
        <?php if (!$is_record) { ?>
            <td><h3 style="text-align: center; color: #0036ff;">No Active Plan Found</h3></td>
        <?php } ?>
    </tr>
    <?php if ($is_emi_completed) { ?>
    <tr>
        <td><h3 style="text-align: center; color:rgb(67 147 56);">Woohoo! You have completed all your EMIs.</h3></td>
    </tr>
    <?php } ?>
    <tr>
<?php foreach ($user_emi_data as $plan_name => $rows) { ?>
            <td style="vertical-align:top;" class="emi-details-td">
                <table border="1" cellpadding="5" style="margin:10px;" class="emi-details-2">
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