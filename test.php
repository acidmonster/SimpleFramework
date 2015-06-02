<?php
include_once './engine/base.php';
include_once './engine/extensions.php';
/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

$ext = new Extention("sdfsdfsdf", "sdfsdfsdf", "Sdsdfsdfdsf");
echo $ext->getCount();

$sub = new Subsystem("sfsfsdf", "dfgdfgdfg", "dgdfgdfg", $ext);
$ext->add($sub);
echo $ext->getCount();