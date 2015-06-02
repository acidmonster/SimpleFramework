// Привязка события Submit формы создания нового проекта
$('form#new_project').submit(function() {
    validation.hideError();

    // Проверить заполнение обязательных полей
    if (!validation.validFill(['pr_name',
        'pr_plan_start_date',
        'pr_plan_end_date',
        'pr_comment'])) {
        validation.showError("Не заполнены обязательные поля.");
        return false;
    }

    // Проверить длину наименования проекта.
    if ($('#pr_name').val().length < 20) {
        validation.showError('Укажите более содержательное наименование проекта.');
        return false;
    }
    
    // Проверить длину описания проекта.
    if ($('#pr_comment').val().length < 20) {
        validation.showError('Укажите более содержательное описание проекта.');
        return false;
    }

    // Проверить формат даты
    if (!validation.validDate(['pr_plan_start_date',
        'pr_plan_end_date'])) {
        validation.showError("Значение не является датой.");
        return false;
    }

    // Проверить период планирования
    if (!validation.compareDate('pr_plan_start_date', 'pr_plan_end_date')) {
        validation.showError("Дата окончания проекта не может быть раньше даты начала.");
        return false;
    }

    // Проверить длительность проета
    var sDate = dates.getDateFromString($('#pr_plan_start_date').val());
    var fDate = dates.getDateFromString($('#pr_plan_end_date').val());
    var nDate = dates.changeDate(sDate, 'y', 1);
    
    if (fDate > nDate) {
        validation.showError('Длительность проекта не может быть более одного года.');
        return false;
    }
    
    return true;
}); 

$("#createNewProject").click(function () {
    // Отправить данные на сервер.
    $("form#new_project").submit();
});

$("#cancelNewProject").click(function () {
    alert("Отменить создание проекта");
});