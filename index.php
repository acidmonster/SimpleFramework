<?php
// Includes
include_once './engine/classes/CApp.php';

CApp::initialize();

// Создать объект для заполнения страницы
$page_data = new CPageData();
// Заголовок страницы
$page_data->setTitle("Family Shop. Магазин уютных вещей.");

CApp::loadTemplateByName("shop", $page_data);