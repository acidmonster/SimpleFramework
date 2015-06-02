<?php

include_once filter_input(INPUT_SERVER, 'DOCUMENT_ROOT') . '/engine/app.php';

// Определить переменные по умолчанию
$pr_name = '';
$pr_plan_start_date = '';
$pr_plan_end_date = '';
$pr_comment = '';

// Обработать действие формы
$action = filter_input(INPUT_POST, 'action');

if (isset($action)) {
    switch ($action) {
        // Создание нового проекта
        case "newProject":
            // Получить значения полей
            $pr_name = filter_input(INPUT_POST, 'pr_name', FILTER_SANITIZE_SPECIAL_CHARS);
            $pr_plan_start_date = filter_input(INPUT_POST, 'pr_plan_start_date', FILTER_SANITIZE_SPECIAL_CHARS);
            $pr_plan_end_date = filter_input(INPUT_POST, 'pr_plan_end_date', FILTER_SANITIZE_SPECIAL_CHARS);
            $pr_comment = filter_input(INPUT_POST, 'pr_comment', FILTER_SANITIZE_SPECIAL_CHARS);

            // Создать проект
            $pr_factory = new ProjectsFactory();
            $project = $pr_factory->createNew($pr_name, $pr_plan_start_date, $pr_plan_end_date, $pr_comment);
            
            if (isset($project)) {
                echo $project->getName();
            }
    }
    
}

function form_NewProject() {

    //$ext_index = $app->extensions_factory->getCurrentExtIndex();
    //$ext = $app->extensions_factory->getExtensionByIndex($ext_index);
    $form = '<form id="new_project" action="" method="POST">'
            . '<div class="form-info">'
            . '  <div class="info-title-area">'
            . '     <div class="info-icon"></div>'
            . '     <div class="info-title"><h2>Создание нового проекта</h2></div>'
            . '  </div>'
            . '  <div class="info-body">Для создания нового проекта укажите следующую информацию в полях:<br />'
            . '    <ul>'
            . '      <li><b>Наименование проекта</b> - наименование должно кратко отражать суть того, чему посвящен проект.</li>'
            . '      <li><b>План. дата начала</b> - укажите планируемую дату начала проекта.</li>'
            . '      <li><b>План. дата окончания</b> - укажите планируемую дату оконания проекта. Продолжительность проекта не может быть больше одного года. В дальнейшем проект можно будет пролонгировать.</li>'
            . '      <li><b>Описание</b> - укажите подробную информацию о проекте. Эта информация будет доступна для других пользователей.</li>'
            . '    </ul>'
            . '  </div>'
            . '</div>'
            . '<div class="form-elements">'
            . '  <h1>Новый проект</h1>'
            . '  <input type="hidden" id="action" name="action" value="newProject" />'
            . '  <div class="error-layer"><div class="error-icon"></div><div class="error-message"></div></div>'
            . '  <div class="element"><div class="element-label">*Наименование проекта:</div><div><input class="element-input input-string" maxlength="100" id="pr_name" name="pr_name" type="text" value="' . $GLOBALS['pr_name'] . '" /></div></div>'
            . '  <div class="element"><div class="element-label">*План. дата начала:</div><div><input class="element-input input-date" id="pr_plan_start_date" name="pr_plan_start_date" type="text" value="' . $GLOBALS['pr_plan_start_date'] . '" /></div></div>'
            . '  <div class="element"><div class="element-label">*План. дата окончания:</div><div><input class="element-input input-date" id="pr_plan_end_date" name="pr_plan_end_date" type="text" value="' . $GLOBALS['pr_plan_end_date'] . '" /></div></div>'
            . '  <div class="element"><div class="element-label">*Описание:</div><div><textarea class="element-input input-text" maxlength="1024" id="pr_comment" name="pr_comment">' . $GLOBALS['pr_comment'] . '</textarea></div></div>'
            . '  <div class="element button"><input type="button" name="saveNewProject" value="Сохранить" class="element-button" id="createNewProject"/></div>'
            . '  <div class="element button"><input type="button" name="cancelNewProject" value="Отменить" class="element-button" id="cancelNewProject"/></div>'
            . '</div>'
            . '</form>';
    return $form;
}
