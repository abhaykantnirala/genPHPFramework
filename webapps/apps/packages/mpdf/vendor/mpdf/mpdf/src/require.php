<?php

require_once __DIR__ . '/../../../psr/log/Psr/Log/LoggerAwareInterface.php';
require_once __DIR__ . '/../../../psr/log/Psr/Log/LoggerInterface.php';
require_once __DIR__ . '/../../../psr/log/Psr/Log/NullLogger.php';
foreach (glob(__DIR__."/Config/*.php") as $function) {
    $function = basename($function);
    require_once __DIR__.'/Config/' . $function ;
}
foreach (glob(__DIR__."/Color/*.php") as $function) {
    $function = basename($function);
    require_once __DIR__.'/Color/' . $function ;
}
foreach (glob(__DIR__."/Css/*.php") as $function) {
    $function = basename($function);
    require_once 'Css/' . $function ;
}
foreach (glob(__DIR__."/Image/*.php") as $function) {
    $function = basename($function);
    require_once __DIR__.'/Image/' . $function ;
}
foreach (glob(__DIR__."/Language/*.php") as $function) {
    $function = basename($function);
    require_once __DIR__.'/Language/' . $function ;
}
foreach (glob(__DIR__."/Log/*.php") as $function) {
    $function = basename($function);
    require_once __DIR__.'/Log/' . $function ;
}
foreach (glob(__DIR__."/Fonts/*.php") as $function) {
    $function = basename($function);
    require_once __DIR__.'/Fonts/' . $function;
}
foreach (glob(__DIR__."/Output/*.php") as $function) {
    $function = basename($function);
    require_once __DIR__.'/Output/' . $function ;
}
foreach (glob(__DIR__."/Pdf/*.php") as $function) {
    $function = basename($function);
    require_once __DIR__.'/Pdf/' . $function ;
}
foreach (glob(__DIR__."/Pdf/Protection/*.php") as $function) {
    $function = basename($function);
    require_once __DIR__.'/Pdf/Protection/' . $function;
}
foreach (glob(__DIR__."/QrCode/*.php") as $function) {
    $function = basename($function);
    require_once __DIR__.'/QrCode/' . $function ;
}
foreach (glob(__DIR__."/Utils/*.php") as $function) {
    $function = basename($function);
    require_once __DIR__.'/Utils/' . $function ;
}
foreach (glob(__DIR__."/Conversion/*.php") as $function) {
    $function = basename($function);
    require_once __DIR__.'/Conversion/' . $function ;
}
//require_once 'Ucdn.php';
//require_once 'SizeConverter.php';
//require_once 'Gradient.php';
foreach (glob(__DIR__."/*.php") as $function) {
    $function = basename($function);
    require_once __DIR__.'/' . $function ;
}