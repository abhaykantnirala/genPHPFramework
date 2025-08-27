<!DOCTYPE html>
<html lang="en">

    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
        <title>:: Home ::</title>
        <link rel="icon" href="<?php echo $this->helper->url->baseurl('public/assets/images/fevicon.png')?>" type="<?php echo $this->helper->url->baseurl('public/assets/images/png')?>" sizes="13x13">
        <link rel="manifest" href="manifest.json">
        <link href="<?php echo $this->helper->url->baseurl('public/assets/css/global.css');?>" rel="stylesheet" type="text/css">
        <script>
            window.addEventListener('load', async () => {
                if ('serviceWorker' in navigator) {
                    try {
                        const registration = await navigator.serviceWorker.register('sw.js');
                        console.log('ServiceWorker registration successful with scope: ', registration.scope);
                    } catch (err) {
                        console.error(err);
                    }
                }
            });
        </script>
    </head>

    <body>
        <!-------------Header---------->
        <?php echo $_header;?>
        <?php echo $_body_;?>
        <!-----------Footer sec-------->
        <?php echo $_footer;?>

        <!-- Modal -->
        <div class="modal fade" id="exampleModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered" role="document">
                <div class="modal-content">

                    <div class="modal-body">
                        <h3 class="text-center">Login</h3>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                        <div class="login-box">
                            <div class="form-feild">
                                <input type="text" class="form-control" placeholder="User name" />
                            </div>
                            <div class="form-feild">
                                <input type="text" class="form-control" placeholder="Password" />
                                <p class="text-right"><a href="#" id="for-got" data-toggle="modal" data-target="#for-Modal">Forgot Password</a></p>
                            </div>
                            <div class="form-feild">
                                <button type="submit" class="btn custom-btn">Login</button>
                            </div>
                            <p class="mb-0 text-center">Not a member yet? <a href="#" id="signup" data-toggle="modal" data-target="#reg-Modal">Sign up</a>.</p>

                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="modal fade" id="reg-Modal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered" role="document">
                <div class="modal-content">

                    <div class="modal-body">
                        <h3 class="text-center">Sign Up</h3>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                        <div class="login-box">
                            <div class="form-feild">
                                <input type="text" class="form-control" placeholder="Name" />
                            </div>
                            <div class="form-feild">
                                <input type="text" class="form-control" placeholder="Contact Number" />
                            </div>
                            <div class="form-feild">
                                <input type="text" class="form-control" placeholder="User name" />
                            </div>
                            <div class="form-feild">
                                <input type="text" class="form-control" placeholder="Password" />
                            </div>
                            <div class="form-feild">
                                <input type="text" class="form-control" placeholder="Confirm Password" />
                            </div>
                            <div class="form-feild">
                                <button type="submit" class="btn custom-btn">Sign Up</button>
                            </div>

                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="modal fade" id="for-Modal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered" role="document">
                <div class="modal-content">

                    <div class="modal-body">
                        <h3 class="text-center">Change Password</h3>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                        <div class="login-box">
                            <div class="form-feild">
                                <input type="text" class="form-control" placeholder="New Password" />
                            </div>
                            <div class="form-feild">
                                <input type="text" class="form-control" placeholder="Confitm Password" />
                            </div>
                            <div class="form-feild">
                                <button type="submit" class="btn custom-btn">Change Password</button>
                            </div>

                        </div>
                    </div>
                </div>
            </div>
        </div>
        <script src="<?php echo $this->helper->url->baseurl('public/assets/js/jquery.min.js');?>"></script>
        <script src="<?php echo $this->helper->url->baseurl('public/assets/js/popper.min.js')?>"></script>
        <script src="<?php echo $this->helper->url->baseurl('public/assets/js/bootstrap.min.js');?>"></script>
        <script src="<?php echo $this->helper->url->baseurl('public/assets/js/owl.carousel.js');?>"></script>
        <script src="<?php echo $this->helper->url->baseurl('public/assets/js/main.js');?>"></script>
    </body>
</html>