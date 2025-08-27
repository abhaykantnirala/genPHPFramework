<?php

interface igcookie {

    public function getdata($cookie_key);

    public function setdata($cookie_key, $cookie_value = NULL);

    public function unsetdata($cookie_key);

    public function flashdata($cookie_key);

    public function getalldata();

    public function unsetalldata();

    public function setmaxlife($max_life_value);

    public function getmaxlife();
}
