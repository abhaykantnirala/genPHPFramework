<?php

#users controller
route::group(['prefix' => 'me-admin', 'middleware' => ['adminauth'], 'module' => 'admin', 'module_structure' => TRUE], function () {
    route::normal(['plans/create', 'plans@add_plan'])->names('plans-add');
    route::normal(['plans/list', 'plans@index'])->names('plans-list');
    route::normal(['users/plans-create', 'plans@create_plan'])->names('plans-create');
    route::normal(['users/plans-update', 'plans@update_plan'])->names('plans-update');
    route::normal(['users/plans-edit', 'plans@edit_plan'])->names('plan-edit');
});