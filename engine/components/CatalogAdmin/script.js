/* global validation */

/**
 * Ожидаем загрузку документа
 */

$(document).ready(function () {

    SFCatalogAdmin = {};

    /* =========================================================================
     * События группы каталогов
     * ========================================================================= 
     */

    /* Группа каталога. Событие "Открытие формы каталога" ----------------------*/
    SFCatalogAdmin.openForm = function (id) {
        if (typeof id !== "undefined") {
            group_id_data = "&id=" + id;
        } else {
            group_id_data = "";
        }

        $.ajax({
            type: "GET",
            url: "/engine/components/CatalogAdmin/action.php?action=form" + group_id_data,
            data: "",
            success: function (form) {
                if (form !== "") {
                    element = document.getElementById("SFCatalogFormLayer");
                    element.innerHTML = form;
                    $("#SFCatalogForm").show();
                    $(".sf-form-background").show();
                }
            }
        });
    };
    
    /* Группа каталога. Событие "Закрытие формы группы" ----------------------*/
    SFCatalogAdmin.closeForm = function (form) {
        $(".sf-form-background").hide();
        $("#SFCatalogForm").hide();
        $("#SFCatalogFormLayer").html("");      
    };

    /* Группа каталога. Событие "Добавление" ---------------------------------*/
    SFCatalogAdmin.add = function () {
        SFCatalogAdmin.openForm();
    };

    /* Группа каталога. Событие "Изменение" ----------------------------------*/
    SFCatalogAdmin.edit = function (id) {
        if (typeof id !== "undefined")
            SFCatalogAdmin.openForm(id);
    };

    /* Группа каталога. Событие "Удаление" -----------------------------------*/
    SFCatalogAdmin.delete = function () {
        selected_elements = $('input:checkbox:checked');
        
        if (selected_elements.length !== 0) {
            $.ajax({
                type: "GET",
                url: "/engine/components/CatalogAdmin/action.php?action=confirmDelete",
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
    
    SFCatalogAdmin.confirmedDelete = function() {
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
            url: "/engine/components/CatalogAdmin/action.php?action=delete",
            data: "id=" + id,
            success: function (result) {
                if (result == true) {
                    location.reload();
                };
            }
        });
    };

    /* Группа каталога. Событие "Сохранение" ---------------------------------*/
    SFCatalogAdmin.save = function () {
        // Создание записи
        $.ajax({
            type: "GET",
            url: "/engine/components/CatalogAdmin/action.php?action=save",
            data: "SFCatalogName=" + $("#SFCatalogName").val() + "&SFCatalogComment=" + $("#SFCatalogComment").val() + "&SFCatalogID=" + $("#SFCatalogID").val() + "&SFCatalogGroupName=" + $("#SFCatalogGroupName").val(),
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
        SFCatalogAdmin.add();
    });

    /* Удалить каталог -------------------------------------------------------*/
    $("#SFDelete").click(function () {        
        SFCatalogAdmin.delete();
    });
    
    /* Изменить каталог ------------------------------------------------------*/
    $(".sf-catalog-title").click(function () {
        id = $(this).parent(".sf-catalog-item").children(".sf-catalog-item-id").val();
        SFCatalogAdmin.edit(id);
    });
    
    /* Закрыть форму каталога ------------------------------------------------*/
    $("body").on('click', '#SFCancel', function () {
        SFCatalogAdmin.closeForm();
    });

    /* Проверка заполнения каталога ------------------------------------------*/
    $("body").on('change', "#SFCatalogName", function () {
        catalog_name_el = $("#SFCatalogName");
        catalog_id_el = $("#SFCatalogID");
        $.ajax({
            type: "GET",
            url: "/engine/components/CatalogAdmin/action.php?action=validate",
            data: "SFCatalogName=" + catalog_name_el.val() + "&SFCatalogID=" + catalog_id_el.val(),
            success: function (checked) {
                if (checked == true) {
                    setNormalStyle(catalog_name_el.parent());
                    validation.hideError();
                } else
                {
                    element.focus();
                    setErrorStyle(catalog_name_el.parent());
                    validation.showError("Каталог с таким наименованием уже существет.");
                }
            }
        });
    });

    /* Отправка данных на сервер ---------------------------------------------*/
    $("body").on('click', "#SFSave", function () {
        // Проверить заполнение обязательных полей
        if (!validation.validFill(['SFCatalogName', 'SFCatalogGroupName'])) {
            validation.showError("Не заполнены обязательные поля.");
            return false;
        }

        // обработать данные на сервере
        $('form#SFCatalogForm').submit();
    });

    $("body").on('submit', "form#SFCatalogForm", function () {
        SFCatalogAdmin.save();
    });

    /* Закрытие диалога подтверждения ----------------------------------------*/
    $("body").on('click', '#SFConfNo', function() {
        $(".sf-form-background").hide();
        $("#SFConfirmForm").hide();
        $("#SFCatalogFormLayer").html(""); 
    });
    
    /* Подтверждение удаления ------------------------------------------------*/
    $("body").on('click', '#SFConfYes', function() {
        SFCatalogAdmin.confirmedDelete();
    });
    
});
