<header class="header">
    <div class="header-top">
        <div class="container">
            <div class="header-top-row">
                <div class="header-top-col header-top-left">
                    <ul>
                        <li><a href="#"><i class="fa fa-mobile"></i> 00000-00000</a></li>
                        <li><a href="#"><i class="fa fa-envelope-o"></i>  info@example.com</a></li>
                        <li><a href="#"><i class="fa fa-map-marker"></i> 121 King Street, Melbourne</a></li>
                    </ul>
                </div>
                <div class="header-top-col header-top-right">
                    <ul>
                        <li><a href="#"><i class="fa fa-facebook-square"></i></a></li>
                        <li><a href="#"><i class="fa fa-twitter"></i></a></li>
                        <li><a href="#"><i class="fa fa-linkedin-square"></i></a></li>
                    </ul>
                    <a href="<?php echo $this->helper->url->baseurl('contact');?>" class="btn custom-btn appointment-btn">Contact with us</a>
                </div>
            </div>
        </div>
    </div>
    <div class="container">
        <div class="header-row">
            <div class="logo-col"><a href="<?php echo $this->helper->url->baseurl();?>"><img src="<?php echo $this->helper->url->baseurl('public/assets/images/logo2.png'); ?>" alt="" /></a></div>
            <div class="menu-col" id='cssmenu'>
                <ul>
                    <li><a href="<?php echo $this->helper->url->baseurl(''); ?>">Home</a></li>
                    <li><a href="<?php echo $this->helper->url->baseurl('about'); ?>">About us</a></li>
                    <li><a href="<?php echo $this->helper->url->baseurl('plans'); ?>">Plans</a></li>
                    <li><a href="<?php echo $this->helper->url->baseurl('contact'); ?>">Contact</a></li>
                    <?php if(isset($this->session->getdata('user-session-data')->id)){?>
                    <li><a href="<?php echo $this->helper->url->baseurl('user-dashboard'); ?>">Dashboard</a></li>
                    <?php }?>
                </ul>
                <?php if(isset($this->session->getdata('user-session-data')->id)){?>
                <a href="<?php echo $this->helper->url->baseurl('user-logout'); ?>" class="btn custom-btn login-btn">Logout</a>
                <?php } else {?>
                <button class="btn custom-btn login-btn"  data-toggle="modal" data-target="#exampleModal">Login</button>
                <?php }?>
            </div>
        </div>
    </div>
</header>