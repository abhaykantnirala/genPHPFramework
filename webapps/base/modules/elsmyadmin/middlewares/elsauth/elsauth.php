<?php

class elsauth extends gmiddleware {

    public function auth() {
        if ($this->library->session->getdata('login') !== 'yes') {
            #$this->helper->url->redirect('_els-login');
        }
    }

}
