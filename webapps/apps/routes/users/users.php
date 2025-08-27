<?php

route::group(['prefix' => 'users', 'middleware' => [], 'module' => 'website', 'module_structure' => TRUE], function () {
    route::normal(['login', 'users@login'])->names('user-login');
});

route::group(['prefix' => 'users', 'middleware' => ['userauth'], 'module' => 'website', 'module_structure' => TRUE], function () {
    route::normal(['', 'users@index']);
    route::normal(['dashboard', 'users@index'])->names('user-dashboard');
    route::normal(['logout', 'users@logout'])->names('user-logout');
    route::normal(['password-update', 'users@password_update'])->names('user-password-update');
});
