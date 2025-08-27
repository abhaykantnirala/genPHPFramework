<!-------------banner---------->
<section class="banner banner-inner">
    <img src="<?php echo $this->helper->url->baseurl('public/assets/images/banner-img1.jpg'); ?>" alt="" />
    <h2>Profile & Policy</h2>
</section>


<!-----------Pricing plan sec-------->
<section class="sec">
    <div class="container">
        <h2>Profile & Policy Detail</h2>
        <div class="row">
            <div class="col-md-12 my-plans-detail">
                <div class="profile-page">
                    <ul class="nav nav-tabs" id="myTab" role="tablist">
                        <li class="nav-item" role="presentation">
                            <a class="nav-link active" id="home-tab" data-toggle="tab" href="#home" role="tab" aria-controls="home" aria-selected="true">Persnal Info</a>
                        </li>
                        <li class="nav-item" role="presentation">
                            <a class="nav-link" id="profile-tab" data-toggle="tab" href="#profile" role="tab" aria-controls="profile" aria-selected="false">Policy Details</a>
                        </li>
                        <!--                        <li class="nav-item" role="presentation">
                                                    <a class="nav-link" id="contact-tab" data-toggle="tab" href="#contact" role="tab" aria-controls="contact" aria-selected="false">Rewards/Team</a>
                                                </li>-->
                    </ul>
                    <div class="tab-content" id="myTabContent">
                        <div class="tab-pane fade show active" id="home" role="tabpanel" aria-labelledby="home-tab">
                            <div class="profile-row">
                                <div class="profile-img">
                                    <h5 class="text-center mt-3"><?php echo ucwords(implode(' ', [$udata->fname, $udata->lname])); ?></h5>
                                    <hr>
                                    <div class="pro-img">
                                        <img src="<?php echo $this->helper->url->baseurl('public/images/users-pic/' . $udata->id . '.png'); ?>" alt="" />
                                    </div>
                                </div>
                                <div class="profile-detail">
                                    <h4>Details as per polocy</h4>
                                    <p><strong>Address</strong> : <span><?php echo ucwords(implode(' ', [$udata->address_1, $udata->address_2])); ?></span></p>
                                    <p><strong>Phone</strong> : <span><?php echo $udata->phone; ?></span></p>
                                    <?php if (!empty($udata->email)) { ?>
                                        <p><strong>Email</strong> : <span><?php echo $udata->email; ?></span></p>
                                    <?php } ?>
                                    <?php if (!empty($udata->aadhaar_number)) { ?>
                                        <p><strong>Aadhaar No</strong> : <span><?php echo $udata->aadhaar_number; ?></span></p>
                                    <?php } ?>
                                    <?php if (!empty($udata->pan_number)) { ?>
                                        <p><strong>PAN No</strong> : <span><?php echo $udata->pan_number; ?></span></p>
                                    <?php } ?>
                                    <?php if (!empty($udata->datetime)) { ?>
                                        <p><strong>Registration Date</strong> : <span><?php echo date('d M, Y', strtotime($udata->datetime)); ?></span></p>
                                    <?php } ?>
                                    <p><strong><a href="#" id="for-got" data-toggle="modal" data-target="#change-pssword-Modal">Update Password</a></strong></p>
                                    
                                    <?php if (!empty($udata->tmppwd) && $udata->tmppwd!='updated by user') { ?>
                                    <h5 style="color:red;">** Please update your password!!!</h5>
                                    <?php } ?>
                                    
                                </div>
                            </div>
                        </div>
                        <div class="tab-pane fade" id="profile" role="tabpanel" aria-labelledby="profile-tab">
                            <div class="emi-details-0"><?php echo $policy_details; ?></div>
                            <div>
                                <div class="tab-pane fade" id="contact" role="tabpanel" aria-labelledby="contact-tab">
                                    <div style="display: none;">
                                        <h4>Rewards/Team</h4>
                                        <div class="table-responsive">
                                            <table class="border table-bordered" width="100%" border="0" cellspacing="5" cellpadding="0">
                                                <tr>
                                                    <th colspan="16">Ravikant</th>

                                                </tr>
                                                <tr>
                                                    <td colspan="8" width="50%">Sunil Pandey</td>
                                                    <td colspan="8">Ram Kumar</td>
                                                </tr>
                                                <tr>
                                                    <td colspan="4" width="25%">Karkit</td>
                                                    <td colspan="4" width="25%">Sandip</td>
                                                    <td colspan="4" width="25%">Bharat</td>
                                                    <td colspan="4" width="25%">Sachin</td>

                                                </tr>
                                                <tr>
                                                    <td colspan="2">Lokesh</td>
                                                    <td colspan="2">Praful</td>
                                                    <td colspan="2">Satees</td>
                                                    <td colspan="2">Arpit</td>
                                                    <td colspan="2">Pooja</td>
                                                    <td colspan="2">Mansi</td>
                                                    <td colspan="2">Govind</td>
                                                    <td colspan="2">Mahendra</td>
                                                </tr>
                                                <tr>
                                                    <td>Govind</td>
                                                    <td>Mahendra</td>
                                                    <td>Dilip</td>
                                                    <td>Pratap</td>
                                                    <td>Nivas</td>
                                                    <td>Jayram</td>
                                                    <td>Govind</td>
                                                    <td>Mahendra</td>
                                                    <td>Dilip</td>
                                                    <td>Pratap</td>
                                                    <td>Nivas</td>
                                                    <td>Jayram</td>
                                                    <td>Dilip</td>
                                                    <td>Pratap</td>
                                                    <td>Nivas</td>
                                                    <td>Jayram</td>
                                                </tr>
                                            </table>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            </section>


            <div class="modal fade" id="change-pssword-Modal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
                <div class="modal-dialog modal-dialog-centered" role="document">
                    <div class="modal-content">
                        <div class="modal-body">
                            <h3 class="text-center">Update Password</h3>
                            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                <span aria-hidden="true">&times;</span>
                            </button>
                            <div class="login-box">
                                <div class="form-feild">
                                    <input type="password" name="u_old_password" id="u_old_password" class="form-control" placeholder="Old Password" />
                                </div>
                                <div class="form-feild">
                                    <input type="password" name="u_new_password" id="u_new_password" class="form-control" placeholder="New Password" />
                                </div>
                                <div class="form-feild">
                                    <button type="button" class="btn custom-btn btn-update-password">Update Password</button>
                                </div>
                                <div class="form-feild" style="color:red;" id="u_change_password_error_success"></div>
                            </div>
                        </div>
                    </div>
                </div>
                <script>
                    $('#u_old_password').val('');
                    $('.btn-update-password').click(function(){
                        u_old_password = $('#u_old_password').val();
                        u_new_password = $('#u_new_password').val();
                        var saveData = $.ajax({
                            type: 'POST',
                            url: "<?php echo $this->helper->url->baseurl('user-password-update');?>",
                            data: {"u_old_password": u_old_password, "u_new_password": u_new_password},
                            success: function(res) { 
                                console.log(res)
                                res = JSON.parse(res);
                                console.log(res);
                                msg = res.msg;
                                $('#u_change_password_error_success').html(msg);
                            }
                        });
                    })
                </script>
            </div>



