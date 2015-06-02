<?php
// Includes
include_once '../engine/app.php';
include_once '../extensions/projects/new_project.php';

// Получить фабрику модулей расширения
$ext_factory = $app->getExtensionsFactory();

// Создать объект для заполнения страницы
$page_data = new PageData();
$page_data->setTitle("Project Manager. Работа с проектами");
$page_data->setExtMenu($ext_factory->getCurrentExtMenu());
$page_data->setSubMenu($ext_factory->getCurrentSubMenu());

//=======================================================//
//              Реализация логики модуля                 //
//=======================================================//

// Получить имя вызываемой подсистемы
$ext_index = $ext_factory->getCurrentExtIndex();
$sub_index = $ext_factory->getCurrentSubIndex();
$sub = $ext_factory->getSubsystemByIndex($ext_index, $sub_index);

switch ($sub->getName()) {
    case "new_project":
        $page_data->setForm(form_NewProject());

        break;

    default:
        break;
}



include('../engine/templates/tmp_main_page.html');