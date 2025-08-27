<?php

interface igsession {

    public function getdata($session_key);

    public function setdata($session_key, $session_value = NULL);

    public function unsetdata($session_key);

    public function flashdata($session_key);

    public function getalldata();

    public function unsetalldata();

    public function setmaxlife($max_life_value);

    public function getmaxlife();

    public function getcdata($session_key);

    public function setcdata($session_key, $session_value = NULL);

    public function unsetcdata($session_key);

    public function flashcdata($session_key);
}
