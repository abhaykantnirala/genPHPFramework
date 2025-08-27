<!-------------banner---------->
<section class="banner banner-inner">
    <img src="<?php echo $this->helper->url->baseurl('public/assets/images/banner-img1.jpg'); ?>" alt="" />
    <h2>Contact us</h2>
</section>

<!-----------about sec-------->
<section class="sec">
    <div class="container">
        <div class="row">
            <div class="col-md-6">
                <div class="contact-left">
                    <h2 class="mb-4">Contact Us</h2>
                    <p class="footer-adr">
                        <i class="fa fa-phone"></i> 
                        <span>Call</span>
                        00000-00000
                    </p>
                    <p class="footer-adr">
                        <i class="fa fa-envelope-o"></i>   
                        <span>Call</span>
                        info@example.com
                    </p>
                    <p class="footer-adr">
                        <i class="fa fa-map-marker"></i>  
                        <span>Call</span>
                        121 King Street, Melbourne
                    </p>
                    <ul class="social-link">
                        <li><a href="#"><i class="fa fa-facebook-square"></i></a></li>
                        <li><a href="#"><i class="fa fa-twitter"></i></a></li>
                        <li><a href="#"><i class="fa fa-linkedin-square"></i></a></li>
                    </ul>
                </div>
            </div>
            <div class="col-md-6">
                <div class="contact-right">
                    <h2 class="mb-4">Write a Message</h2>
                    <div class="row">
                        <div class="col-lg-6 form-col">
                            <input type="text" name="c_name" id="c_name" placeholder="Name" class="form-control" />
                        </div>
                        <div class="col-lg-6 form-col">
                            <input type="text" name="c_email" id="c_email" placeholder="Email" class="form-control" />
                        </div>
                        <div class="col-lg-6 form-col">
                            <input type="text" name="c_subject" id="c_subject" placeholder="Subject" class="form-control" />
                        </div>
                        <div class="col-lg-6 form-col">
                            <input type="text" name="c_phone" id="c_phone" placeholder="Phone" class="form-control" />
                        </div>
                        <div class="col-lg-12 form-col">
                            <textarea placeholder="" name="c_message" id="c_message" class="form-control"></textarea>
                        </div>
                        <div class="col-lg-12"><button type="submit" class="btn custom-btn send-contact">Send a message</button></div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section> 