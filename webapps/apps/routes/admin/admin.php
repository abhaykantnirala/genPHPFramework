<?php
#admin controller
route::group(['prefix' => 'me-admin', 'middleware' => ['adminauth'], 'module' => 'admin', 'module_structure' => TRUE], function () {
    route::normal(['', 'admin@index'])->names('admin-home');
    route::normal(['dashboard', 'admin@index'])->names('admin-dashboard');
    route::normal(['logout', 'admin@logout'])->names('admin-logout');
});

#users controller
route::group(['prefix' => 'me-admin', 'middleware' => ['adminauth'], 'module' => 'admin', 'module_structure' => TRUE], function () {
    route::normal(['users/registration', 'users@registration'])->names('users-registration');
    route::normal(['users/list', 'users@index'])->names('users-list');
    route::normal(['users/plan-allotment/{uid}', 'users@plan_allotment']);
    route::normal(['users/allot/plan', 'users@allot_plan'])->names('allot-plan');
    route::normal(['users/update-EMI/{uid}', 'users@emi_updation_form']);
    route::normal(['users/update-user-EMI', 'users@user_emi_update'])->names('user-emi-update');
    route::normal(['users/EMI-record/{uid}', 'users@user_emi_record']);
    route::normal(['users/do-registration', 'users@do_registration'])->names('users-do-registration');
    route::normal(['users/emi', 'users@view_emi'])->names('user-emi');
    route::normal(['users/get-emi/{uid}', 'users@get_user_emi']);
    route::normal(['contact-us/customers/request', 'contactus@customer_contact_request'])->names('customer-contact-request');
    route::normal(['contact-us/customers/get/request', 'contactus@get_customer_contact_request'])->names('get-customer-contact-request');
});

#outer login 
route::group(['prefix' => 'me-admin', 'middleware' => [], 'module' => 'admin', 'module_structure' => TRUE], function () {
    route::normal(['sign-in', 'admin@sign_in'])->names('admin-signin');
    route::normal(['login', 'admin@login'])->names('admin-login');
});

