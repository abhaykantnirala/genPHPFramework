<aside class="left-sidebar sidebar-dark" id="left-sidebar">
    <div id="sidebar" class="sidebar sidebar-with-footer">
        <!-- Aplication Brand -->
        <div class="app-brand">
            <a href="<?php echo $this->helper->url->baseurl('admin-home'); ?>">
                <img src="<?php echo $this->helper->url->baseurl('public/admin/images/logo.png'); ?>" alt="Mono">
                <span class="brand-name">MONO</span>
            </a>
        </div>
        <!-- begin sidebar scrollbar -->
        <div class="sidebar-left" data-simplebar style="height: 100%;">
            <!-- sidebar menu -->
            <ul class="nav sidebar-inner" id="sidebar-menu">
                <li
                    class="active"
                    >
                    <a class="sidenav-item-link" href="<?php echo $this->helper->url->baseurl('admin-dashboard'); ?>">
                        <i class="mdi mdi-chart-line"></i>
                        <span class="nav-text">Dashboard</span>
                    </a>
                </li>

                <!--                <li class="section-title">
                                    User Section
                                </li>-->

                <li  class="has-sub" >
                    <a class="sidenav-item-link" href="javascript:void(0)" data-toggle="collapse" data-target="#users" aria-expanded="false" aria-controls="users">
                        <i class="mdi mdi-account"></i>
                        <span class="nav-text">User</span> <b class="caret"></b>
                    </a>
                    <ul  class="collapse"  id="users" data-parent="#sidebar-menu">
                        <div class="sub-menu">
                           
                            <li >
                                <a class="sidenav-item-link" href="<?php echo $this->helper->url->baseurl('users-registration'); ?>">
                                    <span class="nav-text">User Registration</span>

                                </a>
                            </li>
                            
                            <li >
                                <a class="sidenav-item-link" href="<?php echo $this->helper->url->baseurl('users-list'); ?>">
                                    <span class="nav-text">User List</span>

                                </a>
                            </li>
                            
                            <li >
                                <a class="sidenav-item-link" href="<?php echo $this->helper->url->baseurl('user-emi'); ?>">
                                    <span class="nav-text">View User EMI</span>

                                </a>
                            </li>

                        </div>
                    </ul>
                </li>

                <!--                <li class="section-title">
                                    Plans Section
                                </li>-->

                <li  class="has-sub" >
                    <a class="sidenav-item-link" href="javascript:void(0)" data-toggle="collapse" data-target="#plans" aria-expanded="false" aria-controls="plans">
                        <i class="mdi mdi-account"></i>
                        <span class="nav-text">Plans</span> <b class="caret"></b>
                    </a>
                    <ul  class="collapse"  id="plans" data-parent="#sidebar-menu">
                        <div class="sub-menu">

                            <li >
                                <a class="sidenav-item-link" href="<?php echo $this->helper->url->baseurl('plans-list'); ?>">
                                    <span class="nav-text">Plans List</span>

                                </a>
                            </li>

                            <li >
                                <a class="sidenav-item-link" href="<?php echo $this->helper->url->baseurl('plans-add'); ?>">
                                    <span class="nav-text">Add Plans</span>

                                </a>
                            </li>

                        </div>
                    </ul>
                </li>
                
                <li  class="has-sub" >
                    <a class="sidenav-item-link" href="javascript:void(0)" data-toggle="collapse" data-target="#contactus" aria-expanded="false" aria-controls="contactus">
                        <i class="mdi mdi-account"></i>
                        <span class="nav-text">Contact Us</span> <b class="caret"></b>
                    </a>
                    <ul  class="collapse"  id="contactus" data-parent="#sidebar-menu">
                        <div class="sub-menu">

                            <li >
                                <a class="sidenav-item-link" href="<?php echo $this->helper->url->baseurl('customer-contact-request'); ?>">
                                    <span class="nav-text">Customer Request</span>

                                </a>
                            </li>

                        </div>
                    </ul>
                </li>
                
                

            </ul>

        </div>

        <div class="sidebar-footer">
            <div class="sidebar-footer-content">
                <ul class="d-flex">
                    <li>
                        <a href="user-account-settings.html" data-toggle="tooltip" title="Profile settings"><i class="mdi mdi-settings"></i></a></li>
                    <li>
                        <a href="#" data-toggle="tooltip" title="No chat messages"><i class="mdi mdi-chat-processing"></i></a>
                    </li>
                </ul>
            </div>
        </div>
    </div>
</aside>