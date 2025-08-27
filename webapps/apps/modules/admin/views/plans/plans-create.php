<div class="card-body m-pd-0">
    <div class="col-xl-12">
        <!-- Billing Information -->
        <div class="card card-default">
            <div class="card-header">
                <h2 class="mb-5"><?php
                    if ($action == "update") {
                        echo "Update ";
                    } else {
                        echo "Create";
                    };
                    ?>Plans</h2>

            </div>
            <div class="card-body">
                <div class="media media-sm">
                    <div class="media-sm-wrapper">
                        <?php if ($action == "update") { ?>
                            <img style="width:80px; height:80px" src="<?php echo $this->helper->url->baseurl('public/images/plans-pic/' . $plan_data->id . '.png'); ?>" alt="User Image"> 
                        <?php } else { ?>
                            <img src="<?php echo $this->helper->url->baseurl('public/admin/images/user/user-sm-01.jpg'); ?>" alt="User Image">
                        <?php } ?>
                    </div>
                    <div class="media-body">
                        <p>Click the current avatar to upload plans photo.</p>
                    </div>
                </div>
                <?php if ($action == "update") { ?>
                <form action="<?php echo $this->helper->url->baseurl('plan-edit'); ?>" method="post" enctype="multipart/form-data" >
                        <input type="hidden" class="form-control" value="<?php echo $plan_data->id; ?>" id="plan_id" name="plan_id">
                    <?php } else { ?>
                        <form action="<?php echo $this->helper->url->baseurl('plans-create'); ?>" method="post" enctype="multipart/form-data" >
                        <?php } ?>
                        <div class="row">
                            <div class="col-lg-4">
                                <label for="coverImage" class="">Cover Image</label>
                                <div class="">
                                    <div class="custom-file mb-1">
                                        <input type="file" class="custom-file-input" name="plans-picture" id="coverImage" requireds>
                                        <label class="custom-file-label" for="coverImage">Choose file...</label>
                                        <div class="invalid-feedback">Example invalid custom file feedback</div>
                                    </div>
                                    <span class="d-block ">Upload a new cover image, JPG 1200x300</span>
                                </div>
                            </div>
                            <div class="col-lg-4">
                                <div class="form-group">
                                    <label for="firstName">Plan Name</label>
                                    <input type="text" value="<?php
                                    if (isset($plan_data->plan_name)) {
                                        echo $plan_data->plan_name;
                                    }
                                    ?>" class="form-control" id="plan_name" name="plan_name">
                                </div>
                            </div>
                            <div class="col-lg-2">
                                <div class="form-group">
                                    <label for="plan_amount">Plan Amount (Rs)</label>
                                    <input type="number" min="1000" value="<?php
                                    if (isset($plan_data->plan_amount)) {
                                        echo $plan_data->plan_amount;
                                    } else {
                                        echo "1000";
                                    }
                                    ?>" class="form-control" id="plan_amount" name="plan_amount">
                                </div>
                            </div>
                            <div class="col-lg-2">
                                <div class="form-group">
                                    <label for="plan_emi">Plan EMI (Rs)</label>
                                    <input type="number" min="500" value="<?php
                                    if (isset($plan_data->plan_emi)) {
                                        echo $plan_data->plan_emi;
                                    } else {
                                        echo "500";
                                    }
                                    ?>" class="form-control" id="plan_emi" name="plan_emi">
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-lg-6">
                                <div class="form-group">
                                    <label for="firstName">Plan Description</label>
                                    <input type="text" value="<?php
                                    if (isset($plan_data->description)) {
                                        echo $plan_data->description;
                                    }
                                    ?>" class="form-control" id="plan_desc" name="plan_desc" required>
                                </div>
                            </div>
                            <div class="col-lg-6">
                                <label class="text-dark font-weight-medium">Plan Duration</label>
                                <div class="form-group">
                                    <select class="country form-control" name="plan_duration" required>
                                        <?php for ($i = 1; $i <= 60; $i++) { ?>
                                            <option value="<?php echo $i; ?>" <?php if ((isset($plan_data->plan_duration) && $plan_data->plan_duration == $i) || $i == 12) { ?> selected="selected"<?php } ?>><?php echo $i; ?> Month<?php
                                                if ($i > 1) {
                                                    echo "s";
                                                }
                                                ?></option>
                                        <?php } ?>
                                    </select> 
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-lg-6">
                                <label class="text-dark font-weight-medium">Plan Type</label>
                                <div class="form-group">
                                    <select class="country form-control" name="plan_emi_type" required>
                                        <option value="daily" <?php if (isset($plan_data->plan_emi_type) && $plan_data->plan_emi_type == "daily") { ?> selected="selected" <?php } ?>>Daily</option>
                                        <option value="monthly" <?php if (isset($plan_data->plan_emi_type) && $plan_data->plan_emi_type == "monthly") { ?> selected="selected" <?php } ?>>Monthly</option>
                                        <option value="quaterly" <?php if (isset($plan_data->plan_emi_type) && $plan_data->plan_emi_type == "quaterly") { ?> selected="selected" <?php } ?>>Quaterly</option>
                                        <option value="half_yearly" <?php if (isset($plan_data->plan_emi_type) && $plan_data->plan_emi_type == "half_yearly") { ?> selected="selected" <?php } ?>>Half Yearly</option>
                                        <option value="yearly" <?php if (isset($plan_data->plan_emi_type) && $plan_data->plan_emi_type == "yearly") { ?> selected="selected" <?php } ?>>Yearly</option>
                                    </select>
                                </div>
                            </div>
                            <div class="col-lg-6">
                                <label class="text-dark font-weight-medium">Priority</label>
                                <div class="form-group">
                                    <input type="number" min="1" value="<?php
                                    if (isset($plan_data->priority)) {
                                        echo $plan_data->priority;
                                    }
                                    ?>" class="form-control" id="priority" name="priority">
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-lg-6">
                                <label class="text-dark font-weight-medium">Status</label>
                                <div class="form-group">
                                    <select class="country form-control" name="status" required>
                                        <option value="1"  <?php if (isset($plan_data->status) && $plan_data->status == "1") { ?> selected="selected" <?php } ?>>Active</option>
                                        <option value="0"  <?php if (isset($plan_data->status) && $plan_data->status == "0") { ?> selected="selected" <?php } ?>>Inactive</option>
                                    </select>
                                </div>
                            </div>

                            <div class="col-lg-6">
                                <label class="text-dark font-weight-medium">Availability</label>
                                <div class="form-group">
                                    <select class="country form-control" name="availability" required>
                                        <option value="1" <?php if (isset($plan_data->availability) && $plan_data->availability == "1") { ?> selected="selected" <?php } ?>>Available</option>
                                        <option value="0" <?php if (isset($plan_data->availability) && $plan_data->availability == "0") { ?> selected="selected" <?php } ?>>Not available</option>
                                    </select>
                                </div>
                            </div>
                        </div>

                        <div class="d-flex justify-content-end mt-6">
                            <button type="submit" class="btn btn-primary mb-2 btn-pill">Submit</button>
                        </div>
                    </form>
            </div>
        </div>
    </div>
</div>