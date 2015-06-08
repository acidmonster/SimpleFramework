/* global validation */

/**
 * Ожидаем загрузку документа
 */

$(document).ready(function () {

    SFGroupAdmin = {};

    /* =========================================================================
     * События группы каталогов
     * ========================================================================= 
     */

    /* Группа каталога. Событие "Открытие формы группы" ----------------------*/
    SFGroupAdmin.openForm = function (id) {
        if (typeof id !== "undefined") {
            group_id_data = "&id=" + id;
        } else {
            group_id_data = "";
        }

        $.ajax({
            type: "GET",
            url: "/engine/components/GroupAdmin/action.php?action=form" + group_id_data,
            data: "",
            success: function (form) {
                if (form !== "") {
                    element = document.getElementById("SFCatalogFormLayer");
                    element.innerHTML = form;
                    $("#SFGroupForm").show();
                    $(".sf-form-background").show();
                }
            }
        });
    };

    /* Группа каталога. Событие "Закрытие формы группы" ----------------------*/
    SFGroupAdmin.closeForm = function (form) {
        $(".sf-form-background").hide();
        $("#SFGroupForm").hide();
        $("#SFCatalogFormLayer").html("");      
    };

    /* Группа каталога. Событие "Добавление" ---------------------------------*/
    SFGroupAdmin.add = function () {
        SFGroupAdmin.openForm();
    };

    /* Группа каталога. Событие "Изменение" ----------------------------------*/
    SFGroupAdmin.edit = function (id) {
        if (typeof id !== "undefined")
            SFGroupAdmin.openForm(id);
    };

    /* Группа каталога. Событие "Удаление" -----------------------------------*/
    SFGroupAdmin.delete = function () {
        selected_elements = $('input:checkbox:checked');
        
        if (selected_elements.length !== 0) {
            $.ajax({
                type: "GET",
                url: "/engine/components/GroupAdmin/action.php?action=confirmDelete",
                data: "",
                success: function (form) {
                    if (form !== "") {
                        element = document.getElementById("SFCatalogFormLayer");
                        element.innerHTML = form;
                        $("#SFConfirmForm").show();
                        $(".sf-form-background").show();
                    };
                }
            });
        }
    };
    
    SFGroupAdmin.confirmedDelete = function() {
        id = "";
        selected_elements = $('input:checkbox:checked');

        selected_elements.each(function() {
            if (id === "") { 
                id = $(this).val(); 
            } else {
                id = id + "," + $(this).val(); 
            }
        });

        $.ajax({
            type: "GET",
            url: "/engine/components/GroupAdmin/action.php?action=delete",
            data: "id=" + id,
            success: function (result) {
                if (result == true) {
                    location.reload();
                };
            }
        });
    };

    /* Группа каталога. Событие "Сохранение" ---------------------------------*/
    SFGroupAdmin.save = function () {
        // Создание записи
        $.ajax({
            type: "GET",
            url: "/engine/components/GroupAdmin/action.php?action=save",
            data: "SFGroupName=" + $("#SFGroupName").val() + "&SFGroupComment=" + $("#SFGroupComment").val() + "&SFGroupID=" + $("#SFGroupID").val(),
            success: function (created) {
                if (created) {
                    location.reload();
                } else
                {
                    validation.showError("Ошибка при сохранении данных.");
                }
            }
        });
    };


    /* =========================================================================
     * Привязки к событиям
     * ========================================================================= 
     */

    /* Добавление записи -----------------------------------------------------*/
    $("#SFAdd").click(function () {
        SFGroupAdmin.add();
    });

    /* Удалить группы -------------------------------------------------------*/
    $("#SFDelete").click(function () {        
        SFGroupAdmin.delete();
    });

    /* Изменить группу -------------------------------------------------------*/
    $(".sf-catalog-group-title").click(function () {
        id = $(this).parent(".sf-catalog-group-item").children(".sf-catalog-group-item-id").val();
        SFGroupAdmin.edit(id);
    });


    /* Закрыть форму группы---------------------------------------------------*/
    $("body").on('click', '#SFCancel', function () {
        SFGroupAdmin.closeForm();
    });

    /* Проверка заполнения группы --------------------------------------------*/
    $("body").on('change', "#SFGroupName", function () {
        group_name_el = $("#SFGroupName");
        group_id_el = $("#SFGroupID");

        $.ajax({
            type: "GET",
            url: "/engine/components/GroupAdmin/action.php?action=validate",
            data: "SFGroupName=" + group_name_el.val() + "&SFGroupID=" + group_id_el.val(),
            success: function (checked) {

                if (checked == true) {
                    setNormalStyle(group_name_el.parent());
                    validation.hideError();
                } else
                {
                    element.focus();
                    setErrorStyle(group_name_el.parent());
                    validation.showError("Группа с таким наименованием уже существет.");
                }
            }
        });
    });

    /* Отправка данных на сервер ---------------------------------------------*/
    $("body").on('click', "#SFSave", function () {
        // Проверить заполнение обязательных полей
        if (!validation.validFill(['SFGroupName'])) {
            validation.showError("Не заполнены обязательные поля.");
            return false;
        }

        // обработать данные на сервере
        $('form#SFGroupForm').submit();
    });

    $("body").on('submit', "form#SFGroupForm", function () {
        SFGroupAdmin.save();
    });

    /* Закрытие диалога подтверждения ----------------------------------------*/
    $("body").on('click', '#SFConfNo', function() {
        $(".sf-form-background").hide();
        $("#SFConfirmForm").hide();
        $("#SFCatalogFormLayer").html(""); 
    });
    
    /* Подтверждение удаления ------------------------------------------------*/
    $("body").on('click', '#SFConfYes', function() {
        SFGroupAdmin.confirmedDelete();
    });
});
