<div class="card-body">
    <div class="col-xl-12">
        <!-- Billing Information -->
        <div class="card card-default">
            <div class="card-header">
                <h2 class="mb-5">EMI Update</h2>
            </div>
            <div class="card-body">
                <div class="media media-sm">
                    <div class="media-sm-wrapper">
                        <img height="100px" width="100px" src="<?php echo $this->helper->url->baseurl('public/images/users-pic/' . $user_data->uid . '.png'); ?>" alt="User Image">
                    </div>

                </div>
                <form action="<?php echo $this->helper->url->baseurl('user-emi-update'); ?>" method="post" enctype="multipart/form-data" >
                    <div class="row">
                        <div style="height: 50px;" class="col-lg-12"></div>
                    </div>
                    <input type="hidden" name="user_id" value="<?php echo $user_data->uid; ?>">
                    <div class="row">
                        <div class="col-lg-4">
                            <div class="form-group">
                                <table>
                                    <tr>
                                        <td>First Name: </td>
                                        <td> <?php echo ucwords(strtolower($user_data->fname)); ?></td>
                                    </tr>
                                    <tr>
                                        <td>Last Name: </td>
                                        <td> <?php echo ucwords(strtolower($user_data->lname)); ?></td>
                                    </tr>
                                    <tr>
                                        <td>City: </td>
                                        <td> <?php echo ucwords(strtolower($user_data->city)); ?></td>
                                    </tr>
                                    <tr>
                                        <td>Mobile No: </td>
                                        <td> <?php echo ucwords(strtolower($user_data->phone)); ?></td>
                                    </tr>
                                    <tr>
                                        <td>Created At: </td>
                                        <td> <?php echo ucwords(strtolower($user_data->datetime)); ?></td>
                                    </tr>
                                </table>
                            </div>
                        </div>

                        <div class="col-lg-8">
                            <div class="row">
                                <div class="col-lg-6">
                                    <label for="lastName">Select Plan name</label>
                                    <div class="form-group">
                                        <select class="country form-control" name="user_plans_id" required>
                                            <option value="">None</option>
                                            <?php foreach ($plans_list as $row) { ?>
                                                <option value="<?php echo $row->user_plans_id; ?>"><?php echo implode(" - ", [ucwords(strtolower($row->plan_name)), $row->plan_duration . ' Months']) . ' (EMI -' . ucwords(strtolower(str_replace("_", " ", $row->plan_emi_type))) . ')'; ?></option>
                                            <?php } ?>
                                        </select>
                                    </div>
                                </div>
                                <div class="col-lg-6">
                                    <label for="lastName">Received EMI Amount</label>
                                    <div class="form-group">
                                        <input type="number" class="form-control" min="1" value="1" name="emi_amount" id="emi_amount">
                                    </div>
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-lg-6">
                                    <label for="lastName">Select Payment Method</label>
                                    <div class="form-group">
                                        <select class="country form-control" name="payment_method" required>
                                            <option value="">None</option>
                                            <option value="cash">Cash</option>
                                            <option value="bank_transfer">Bank Transfer</option>
                                            <option value="upi">UPI Transfer</option>
                                            <option value="adjustment">Adjustment</option>
                                        </select>
                                    </div>
                                </div>
                                <div class="col-lg-6">
                                    <label for="lastName">Late Fine</label>
                                    <div class="form-group">
                                        <input type="number" class="form-control" min="0" value="0" name="late_fine" id="late_fine" required>
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-lg-6">
                                    <label for="lastName">Comment</label>
                                    <div class="form-group">
                                        <input type="text" class="form-control" min="1" name="comment" id="comment" required>
                                    </div>
                                </div>
                            </div>
                            <div class="d-flex justify-content-end mt-6">
                                <button type="submit" class="btn btn-primary mb-2 btn-pill">Update EMI</button>
                            </div>
                        </div>
                </form>
            </div>
        </div>
    </div>
</div>