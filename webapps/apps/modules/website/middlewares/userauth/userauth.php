<?php

class userauth extends gmiddleware {

    public function auth() {
        #check of user is logged in else redirect to login page
        if (empty($this->session->getdata('user-session-data'))) {
            $this->helper->url->redirect('');
        }
    }

}
