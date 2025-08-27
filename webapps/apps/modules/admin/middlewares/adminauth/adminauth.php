<?php

class adminauth extends gmiddleware {

    public function auth() {
        #check of user is logged in else redirect to login page
        if (empty($this->session->getdata('udata'))) {
            $this->helper->url->redirect('admin-signin');
        }
    }

}
