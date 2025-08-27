<div class="card-body">
    <div class="col-xl-12">
        <!-- Billing Information -->
        <div class="card card-default">
            <div class="card-header">
                <h2 class="mb-5">Plans Settings</h2>

            </div>
            <div class="card-body">
                <div class="media media-sm">
                    <div class="media-sm-wrapper">
                        <img src="<?php echo $this->helper->url->baseurl('public/admin/images/user/user-sm-01.jpg'); ?>" alt="User Image">
                    </div>
                    <div class="media-body">
                        <span class="title h3">The stars are twinkling.</span>
                        <p>Click the current avatar to upload plans photo.</p>
                    </div>
                </div>
                <form action="<?php echo $this->helper->url->baseurl('plans-create'); ?>" method="post" >
                    <div class="row">
                        <div class="col-lg-6">
                            <label for="coverImage" class="col-sm-4 col-lg-2 col-form-label">Cover Image</label>
                            <div class="col-sm-8 col-lg-10">
                                <div class="custom-file mb-1">
                                    <input type="file" class="custom-file-input" name="plans-picture" id="coverImage" requireds>
                                    <label class="custom-file-label" for="coverImage">Choose file...</label>
                                    <div class="invalid-feedback">Example invalid custom file feedback</div>
                                </div>
                                <span class="d-block ">Upload a new cover image, JPG 1200x300</span>
                            </div>
                        </div>
                        <div class="col-lg-6">
                            <div class="form-group">
                                <label for="firstName">Plan Name</label>
                                <input type="text" class="form-control" id="plan_name" name="plan_name">
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-lg-6">
                            <div class="form-group">
                                <label for="firstName">Plan Description</label>
                                <input type="text" class="form-control" id="plan_desc" name="plan_desc" required>
                            </div>
                        </div>
                        <div class="col-lg-6">
                            <label class="text-dark font-weight-medium">Plan Duration</label>
                            <div class="form-group">
                                <select class="country form-control" name="plan_duration" required>
                                    <?php for($i=1; $i<=60; $i++){?>
                                    <option value="<?php echo $i;?>" <?php if($i==12){?> selected="selected"<?php }?>><?php echo $i;?> Month<?php if($i>1){echo "s";}?></option>
                                    <?php }?>
                                </select> 
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-lg-6">
                            <label class="text-dark font-weight-medium">Plan Type</label>
                            <div class="form-group">
                                <select class="country form-control" name="plan_emi_type" required>
                                    <option value="daily">Daily</option>
                                    <option value="monthly" selected="selected">Monthly</option>
                                    <option value="quaterly">Quaterly</option>
                                    <option value="half_yearly">Half Yearly</option>
                                    <option value="yearly">Yearly</option>
                                </select>
                            </div>
                        </div>
                        <div class="col-lg-6">
                            <label class="text-dark font-weight-medium">Priority</label>
                            <div class="form-group">
                                <input type="number" min="0" class="form-control" id="priority" name="priority">
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-lg-6">
                            <label class="text-dark font-weight-medium">Status</label>
                            <div class="form-group">
                                <select class="country form-control" name="status" required>
                                    <option value="1">Active</option>
                                    <option value="0" selected="selected">Inactive</option>
                                </select>
                            </div>
                        </div>

                        <div class="col-lg-6">
                            <label class="text-dark font-weight-medium">Availability</label>
                            <div class="form-group">
                                <select class="country form-control" name="availability" required>
                                    <option value="1">Available</option>
                                    <option value="0" selected="selected">Not available</option>
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