<?php

route::group(['prefix' => '', 'middleware' => [], 'module' => 'website', 'module_structure' => TRUE], function () {
    route::normal(['', 'website@index']);
    route::normal(['home', 'website@index'])->names('home');
    route::normal(['about', 'website@about'])->names('about');
    route::normal(['plans-detail', 'website@plansdetail'])->names('plans-detail');
    route::normal(['plans', 'website@plans'])->names('plans');
    route::normal(['contact', 'website@contact'])->names('contact');
    route::normal(['terms-and-conditions', 'website@terms'])->names('terms');
    route::normal(['privacy-policy', 'website@privacy_policy'])->names('privacy-policy');
    route::normal(['sendmail', 'website@sendmail'])->names('sendmail');
});
