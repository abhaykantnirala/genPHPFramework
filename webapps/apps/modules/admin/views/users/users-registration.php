<div class="card-body  m-pd-0">
    <div class="col-xl-12">
        <!-- Billing Information -->
        <div class="card card-default">
            <div class="card-header">
                <h2 class="mb-5">user profile settings</h2>

            </div>
            <div class="card-body">
                <div class="media media-sm">
                    <div class="media-sm-wrapper">
                        <img src="<?php echo $this->helper->url->baseurl('public/admin/images/user/user-sm-01.jpg'); ?>" alt="User Image">
                    </div>
                    <div class="media-body">
                        <p>Click the current avatar to change your photo.<span class="mandatory">*</span></p>
                    </div>
                </div>
                <form action="<?php echo $this->helper->url->baseurl('users-do-registration'); ?>" method="post" enctype="multipart/form-data" >
                    <div class="row">
                        <div class="col-lg-6">
                            <label for="coverImage" class="">Cover Image</label>
                            <div class="">
                                <div class="custom-file mb-1">
                                    <input type="file" class="custom-file-input" name="user-picture" id="coverImage" requireds>
                                    <label class="custom-file-label" for="coverImage">Choose file...</label>
                                    <div class="invalid-feedback">Example invalid custom file feedback</div>
                                </div>
                                <span class="d-block ">Upload a new cover image, JPG 1200x300</span>
                            </div>
                        </div>
                        <div class="col-lg-6">
                            <div class="form-group">
                                <label for="firstName">Referral Code</label>
                                <input type="text" class="form-control" id="rf_code" name="rf_code">
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-lg-6">
                            <div class="form-group">
                                <label for="firstName">First name<span class="mandatory">*</span></label>
                                <input type="text" class="form-control" id="fname" name="fname" required>
                            </div>
                        </div>

                        <div class="col-lg-6">
                            <div class="form-group">
                                <label for="lastName">Last name</label>
                                <input type="text" class="form-control" id="lname" name="lname">
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-lg-6">
                            <div class="form-group">
                                <label for="aadhaarCard">Aadhaar No<span class="mandatory">*</span></label>
                                <input type="text" class="form-control" id="aadhaar_number" name="aadhaar_number" required>
                            </div>
                        </div>

                        <div class="col-lg-6">
                            <div class="form-group">
                                <label for="panNumber">Pan Number</label>
                                <input type="text" class="form-control" id="pan_number" name="pan_number">
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-lg-6">
                            <label class="text-dark font-weight-medium">Country<span class="mandatory">*</span></label>
                            <div class="form-group">
                                <select class="country form-control" name="country_name" required>
                                    <option value="India">India</option>
                                </select>
                            </div>
                        </div>
                        <div class="col-lg-6">
                            <label class="text-dark font-weight-medium">City<span class="mandatory">*</span></label>
                            <div class="form-group">
                                <input type="text" class="form-control" id="city" name="city" required>
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-lg-6">
                            <label class="text-dark font-weight-medium">Address line 1<span class="mandatory">*</span></label>
                            <div class="form-group">
                                <input type="text" class="form-control" id="adress1" name="address1" required>
                            </div>
                        </div>

                        <div class="col-lg-6">
                            <label class="text-dark font-weight-medium">Address line 2</label>
                            <div class="form-group">
                                <input type="text" class="form-control" id="adress2" name="address2">
                            </div>
                        </div>
                    </div>

                    <div class="form-group mb-4">
                        <label for="phone">Phone no<span class="mandatory">*</span></label>
                        <input type="number" class="form-control" min="0" id="phone" name="phone" required>
                    </div>

                    <div class="row mb-2">
                        <div class="col-lg-6">
                            <div class="form-group">
                                <label for="State">State<span class="mandatory">*</span></label>
                                <input type="text" class="form-control" id="state" name="state" required>
                            </div>
                        </div>

                        <div class="col-lg-6">
                            <div class="form-group">
                                <label for="zipCode">Zip code<span class="mandatory">*</span></label>
                                <input type="text" class="form-control" id="zipcode" name="zipcode" required>
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