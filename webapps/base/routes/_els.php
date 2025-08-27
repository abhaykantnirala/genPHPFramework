<?php

route::group(['prefix' => '_elsmyadmin', 'middleware' => [], 'module' => 'elsmyadmin', 'module_structure' => TRUE, 'internal'=>true], function() {
    route::normal(['', '_els@index'])->names('_els');
    route::normal(['_fetchdata', '_els@fetchdata'])->names('_fetchdata');
    route::normal(['login', '_els@login'])->names('_els-login');
});
