<div class="card-body">
    <div class="col-xl-12">
        <!-- Billing Information -->
        <div class="card card-default">
            <div class="card-header">
                <h2 class="mb-5">Plan Allotment</h2>

            </div>
            <?php if(!isset($user_data->uid)){?>
            <h1 style="color:red;">User not found!!!</h1>
            <?php }else{?>
            <div class="card-body">
                <div class="media media-sm">
                    <div class="media-sm-wrapper">
                        <img height="100px" width="100px" src="<?php echo $this->helper->url->baseurl('public/images/users-pic/' . $user_data->uid . '.png'); ?>" alt="User Image">
                    </div>

                </div>
                
                <form action="<?php echo $this->helper->url->baseurl('allot-plan'); ?>" method="post" enctype="multipart/form-data" >
                    <div class="row">
                        <div style="height: 20px;" class="col-lg-12"></div>
                    </div>
                    <input type="hidden" name="user_id" value="<?php echo $user_data->uid;?>">
                    <div class="row">
                        <div class="col-lg-6">
                            <div class="form-group user-dl">
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
                                    <?php if(count($user_plans)){?>
                                    <tr>
                                        <td>Plans Taken</td>
                                        <td>
                                            <?php
                                            foreach($user_plans as $row){
                                                ?>
                                            <h5 style="color:blue;"><?php echo $row->plan_name;?></h5>
                                            <?php
                                            }
                                            ?>
                                        </td>
                                    </tr>
                                    <?php }?>
                                </table>
                            </div>
                        </div>

                        <div class="col-lg-6">
                            <div class="row">
                                <div class="col-lg-12">
                                    <label for="lastName">Select Plan name</label>
                                    <div class="form-group">
                                        <select class="country form-control" name="plan_id" required>
                                            <option value="">None</option>
                                            <?php foreach ($plans_list as $row) { ?>
                                                <option value="<?php echo $row->id; ?>"><?php echo implode(" - ",[ucwords(strtolower($row->plan_name)), $row->plan_duration.' Months']).' (EMI -'.ucwords(strtolower(str_replace("_"," ", $row->plan_emi_type))).')'; ?></option>
                                            <?php } ?>
                                        </select>
                                    </div>
                                </div>

                            </div>
                            <div class="d-flex justify-content-end mt-6">
                                <button type="submit" class="btn btn-primary mb-2 btn-pill">Allot Plan</button>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
            <?php }?>
        </div>
    </div>
</div>