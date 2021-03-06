<?php
// Includes
include_once '../../engine/classes/CApp.php';

CApp::initialize();

// Проверить авторизацию
CApp::needAuthorize();

// Создать объект для заполнения страницы
$page_data = new CPageData();
// Заголовок страницы
$page_data->setTitle("Настройки \ Каталоги");
// Подготовить контент
$page_data->setContent(CApp::loadComponent("CatalogAdmin"));


CApp::loadTemplateByName("shop", $page_data);