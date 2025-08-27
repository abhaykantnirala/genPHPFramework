<div class="row">
    <div class="col-12">
        <div class="card card-default">
            <div class="card-header">
                <h2>Plans List</h2>
                <div class="dropdown">
                    <a class="dropdown-toggle" href="#" role="button" id="dropdownMenuLink" data-toggle="dropdown" aria-haspopup="true"
                       aria-expanded="false"> Filter
                    </a>

                    <div class="dropdown-menu dropdown-menu-right" aria-labelledby="dropdownMenuLink">
                        <a class="dropdown-item" href="#">Action</a>
                        <a class="dropdown-item" href="#">Another action</a>
                        <a class="dropdown-item" href="#">Something else here</a>
                    </div>
                </div>
                <div class="dropdown">
                    <a href="<?php echo $this->helper->url->baseurl('plans-add'); ?>" aria-haspopup="true" aria-expanded="false">
                        Add Plan +
                    </a>
                </div>
            </div>
            <div class="card-body">
                <table id="productsTable" class="table table-hover table-product" style="width:100%">
                    <thead>
                        <tr>
                            <th></th>
                            <th>Plan Name</th>
                            <th>Amount</th>
                            <th>EMI Type</th>
                            <th>Duration</th>
                            <th>Active</th>
                            <th>Available</th>
                            <th>Date</th>
                            <th></th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        foreach ($plans_list as $row) {
                            ?>
                            <tr>
                                <td class="py-0">
                                    <img src="<?php echo $this->helper->url->baseurl('public/images/plans-pic/'.$row->id.'.png'); ?>" alt="Picture of <?php ucwords(implode(' ', [$row->plan_name])); ?>">
                                </td>
                                <td><?php echo ucwords(implode(' ', [$row->plan_name])); ?></td>
                                <td><?php echo ucwords(implode(' ', ['Rs', $row->plan_amount])); ?></td>
                                <td><?php echo ucwords(str_replace("_", " ", $row->plan_emi_type)); ?></td>
                                <td><?php echo $row->plan_duration.' '.'Months'; ?></td>
                                <td><?php echo $row->statusa?'Active':'Inactive'; ?></td>
                                <td><?php echo $row->statusb?'Available':'Not Available'; ?></td>
                                <td><?php echo date('d M y', strtotime($row->datetime)); ?></td>
                                <td>
                                    <div class="dropdown">
                                        <a class="dropdown-toggle icon-burger-mini" href="#" role="button" id="dropdownMenuLink" data-toggle="dropdown"
                                           aria-haspopup="true" aria-expanded="false">
                                        </a>

                                        <div class="dropdown-menu dropdown-menu-right" aria-labelledby="dropdownMenuLink">
                                            <a class="dropdown-item" href="<?php echo $this->helper->url->baseurl('plans-update'); ?>?pid=<?php echo $row->id;?>">Update</a>
                                        </div>
                                    </div>
                                </td>
                            </tr>
                        <?php } ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>