<div class="row">
    <div class="col-12">
        <div class="card card-default">
            <div class="card-header">
                <h2>Users List</h2>
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
                    <a href="<?php echo $this->helper->url->baseurl('users-registration'); ?>" aria-haspopup="true" aria-expanded="false">
                        Add User +
                    </a>
                </div>
            </div>
            <div class="card-body">
                <table id="productsTable" class="table table-hover table-product" style="width:100%">
                    <thead>
                        <tr>
                            <th>User Pic</th>
                            <th>Name</th>
                            <th>pwd</th>
                            <th>Mobile</th>
                            <th>Email</th>
                            <th>Referral Code</th>
                            <th>Address</th>
                            <th>Date</th>
                            <th></th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        foreach ($users_list as $row) {
                            ?>
                            <tr>
                                <td class="py-0">
                                    <img src="<?php echo $this->helper->url->baseurl('public/images/users-pic/' . $row->uid . '.png'); ?>" alt="Picture of <?php ucwords(implode(' ', [$row->fname, $row->lname])); ?>">
                                </td>
                                <td><?php echo ucwords(implode(' ', [$row->fname, $row->lname])); ?></td>
                                <td><?php echo $row->tmppwd; ?></td>
                                <td><?php echo $row->phone; ?></td>
                                <td><?php echo $row->email; ?></td>
                                <td><?php echo $row->rf_code; ?></td>
                                <td><?php echo ucwords(implode(' ', [$row->address_1, $row->address_2])); ?></td>
                                <td><?php echo date('d M y', strtotime($row->datetime)); ?></td>
                                <td>
                                    <div class="dropdown">
                                        <a class="dropdown-toggle icon-burger-mini" href="#" role="button" id="dropdownMenuLink" data-toggle="dropdown"
                                           aria-haspopup="true" aria-expanded="false">
                                        </a>

                                        <div class="dropdown-menu dropdown-menu-right" aria-labelledby="dropdownMenuLink">
                                            <a class="dropdown-item" href="<?php echo $this->helper->url->baseurl('me-admin/users/plan-allotment/' . $row->uid); ?>">Allot a new plan</a>
                                            <a class="dropdown-item" href="<?php echo $this->helper->url->baseurl('me-admin/users/update-EMI/' . $row->uid); ?>">Update Received EMI</a>
    <!--                                            <a class="dropdown-item" href="<?php //echo $this->helper->url->baseurl('me-admin/users/EMI-record/'.$row->uid); ?>">View EMI Record</a>-->
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