<div class="card-body m-pd-0">
    <div class="col-xl-12">
        <!-- Billing Information -->
        <div class="card card-default">
            <div class="card-header">
                <h2 class="mb-5">Users EMI</h2>
            </div>
            <div class="card-body">
                <div class="row">
                    <div style="height: 50px;" class="col-lg-12"></div>
                </div>

                <div class="row">
                    <div class="col-lg-12">
                        <div class="row">
                            <div class="col-lg-6">
                                <label for="lastName">Select user</label>
                                <div class="form-group">
                                    <select onchange="show_emi()" class="country form-control" name="user_id" id="user_id" required>
                                        <option value="">None</option>
                                        <?php foreach ($users_list as $user) { ?>
                                            <option value="<?php echo $user->uid; ?>"><?php echo implode(' ', [$user->phone, '(', $user->fname, $user->lname, ')']); ?></option>
                                        <?php } ?>
                                    </select>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-lg-12">
                        <div id="emi-playground"></div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script>
        function show_emi() {
            uid = $('#user_id').val();
            var saveData = $.ajax({
                type: 'POST',
                url: "<?php echo $this->helper->url->baseurl('me-admin/users/get-emi/'); ?>" + uid,
                success: function (res) {
                    $('#emi-playground').html(res);
                }
            });
        }

    </script>