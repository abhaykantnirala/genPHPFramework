<!-------------banner---------->
<section class="banner banner-inner">
    <img src="<?php echo $this->helper->url->baseurl('public/assets/images/banner-img1.jpg'); ?>" alt="" />
    <h2>Plans</h2>
</section>

<!-----------Pricing plan sec-------->
<section class="sec">
    <div class="container text-center">
        <span class="sub-head">PRICING TABLES</span>
        <h2>See Our Pricing Plans</h2>
        <div class="row">
            <?php foreach($plans_list as $row){?>
            <div class="col-md-4 my-plans">
                <div class="pricing-plan-col text-center">
                    <p class="pricing-top"><?php $formatter = new NumberFormatter('en_IN',  NumberFormatter::CURRENCY); echo $formatter->formatCurrency($row->plan_amount, 'INR');?> <span>/ <?php  echo ucwords(implode(' ', [$row->plan_duration, 'Months']));?></span></p>
                    <h4><?php echo $row->plan_name;?></h4>
                    <div class="pricing-con">
                        <?php echo $row->description;?>
<!--                        <ul>
                            <li>Extra features</li>
                            <li>Lifetime free support</li>
                            <li>Upgrate options</li>
                            <li>Full access</li>
                        </ul>-->
                    </div>
                    <a href="#" style="display:none;" class="btn custom-btn">Select Plan</a>
                </div>
            </div>
            <?php }?>
        </div>
    </div>
</section>

<!-----------write sms sec-------->
<section class="sec write-sms-sec">
    <div class="container">
        <div class="row">
            <div class="col-md-6">

            </div>
            <div class="col-md-6">
                <div class="write-sms-content">
                    <span class="sub-head">Contact With Us</span>
                    <h2>Write a Message</h2>
                    <div class="row">
                        <div class="col-lg-6 form-col">
                            <input type="text" placeholder="Name" class="form-control" />
                        </div>
                        <div class="col-lg-6 form-col">
                            <input type="text" placeholder="Email" class="form-control" />
                        </div>
                        <div class="col-lg-6 form-col">
                            <input type="text" placeholder="Subject" class="form-control" />
                        </div>
                        <div class="col-lg-6 form-col">
                            <input type="text" placeholder="Phone" class="form-control" />
                        </div>
                        <div class="col-lg-12 form-col">
                            <textarea placeholder="" class="form-control"></textarea>
                        </div>
                        <div class="col-lg-12"><button type="submit" class="btn custom-btn">Send a message</button></div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section> 