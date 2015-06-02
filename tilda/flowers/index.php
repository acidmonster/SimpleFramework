<?php
// Includes
include_once '../../engine/classes/CApp.php';

CApp::initialize();

// Создать объект для заполнения страницы
$page_data = new CPageData();
// Заголовок страницы
$page_data->setTitle('Кукла "Тильда"');

CApp::loadTemplateByName("shop", $page_data);