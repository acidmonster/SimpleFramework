/* global validation */

/**
 * Ожидаем загрузку документа
 */

$(document).ready(function () {

    SFCatalogAdministrate = {};

    /* =========================================================================
     * События группы каталогов
     * ========================================================================= 
     */

    SFCatalogAdministrate.Group = {};

    /* Группа каталога. Событие "Открытие формы группы" ----------------------*/
    SFCatalogAdministrate.Group.openForm = function (id) {
        if (typeof id !== "undefined") {
            group_id_data = "&id=" + id;
        } else {
            group_id_data = "";
        }

        $.ajax({
            type: "GET",
            url: "/engine/components/CatalogAdministrate/action.php?action=getGroupForm" + group_id_data,
            data: "",
            success: function (form) {
                if (form !== "") {
                    //$("#SFCatalogFormLayer").html(form);
                    element = document.getElementById("SFCatalogFormLayer");
                    element.innerHTML = form;
                    $("#SFGroupForm").show();
                    $(".sf-catalog-background").show();
                }
            }
        });
    };

    /* Группа каталога. Событие "Закрытие формы группы" ----------------------*/
    SFCatalogAdministrate.Group.closeForm = function (form) {
        //if (typeof form !== "undefined") {
        $(".sf-catalog-background").hide();
        $("#SFGroupForm").hide();
        $("#SFCatalogFormLayer").html("");
        //}        
    };

    /* Группа каталога. Событие "Добавление" ---------------------------------*/
    SFCatalogAdministrate.Group.add = function () {
        SFCatalogAdministrate.Group.openForm();
    };

    /* Группа каталога. Событие "Изменение" ----------------------------------*/
    SFCatalogAdministrate.Group.edit = function (id) {
        if (typeof id !== "undefined")
            SFCatalogAdministrate.Group.openForm(id);
    };

    /* Группа каталога. Событие "Удаление" -----------------------------------*/
    SFCatalogAdministrate.Group.delete = function (id) {
        if (typeof id !== "undefined") {

        }
        ;
    };

    /* Группа каталога. Событие "Сохранение" ---------------------------------*/
    SFCatalogAdministrate.Group.Save = function () {
        // Создание записи
        $.ajax({
            type: "GET",
            url: "/engine/components/CatalogAdministrate/action.php?action=saveGroup",
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
     * События каталогов
     * ========================================================================= 
     */


    /* =========================================================================
     * Привязки элементов форм к событиям
     * ========================================================================= 
     */

    /* Добавление новой группы------------------------------------------------*/
    $("#SFAddGroup").click(function () {
        SFCatalogAdministrate.Group.add();
    });

    /* Изменить группу -------------------------------------------------------*/
    $(".sf-catalog-group-item").click(function () {
        id = $(this).children(".sf-catalog-group-item-id").val();
        SFCatalogAdministrate.Group.edit(id);
    });


    /* Закрыть форму группы---------------------------------------------------*/
    $("body").on('click', '#SFCancelGroup', function () {
        SFCatalogAdministrate.Group.closeForm();
    });

    /* Проверка заполнения группы --------------------------------------------*/
    $("body").on('change', "#SFGroupName", function () {
        group_name_el = $("#SFGroupName");
        group_id_el = $("#SFGroupID");

        $.ajax({
            type: "GET",
            url: "/engine/components/CatalogAdministrate/action.php?action=validateGroup",
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
    $("body").on('click', "#SFSaveGroup", function () {
        // Проверить заполнение обязательных полей
        if (!validation.validFill(['SFGroupName'])) {
            validation.showError("Не заполнены обязательные поля.");
            return false;
        }

        // обработать данные на сервере
        $('form#SFGroupForm').submit();
    });

    $("body").on('submit', "form#SFGroupForm", function () {
        SFCatalogAdministrate.Group.Save();
    });

    /* ========================================================================
     * События формы каталогов
     * ========================================================================
     */

    /* Открыть форму каталога ------------------------------------------------*/
    $("#SFAddCatalog").click(function () {
        // Создание записи
        $.ajax({
            type: "GET",
            url: "/engine/components/CatalogAdministrate/action.php?action=get_groups_list",
            data: "",
            success: function (html) {
                if (html !== "") {
                    $("#SFCatalogGroupName").html(html);
                }

                $("#SFCatalogForm").show();
                $(".sf-catalog-background").show();
            }
        });
    });


    /* Закрыть форму каталога -------------------------------------------------*/
    $("#SFCancelCatalog").click(function () {
        $(".sf-catalog-background").hide();
        $("#SFCatalogForm").hide();
        $("#SFCatalogForm")[0].reset();
        setNormalStyle($("#SFCatalogName").parent());
        $(".sf-catalog-error-box").hide();
        $(".sf-catalog-error-box").text("");
    });

    /* Отправка данных на сервер ---------------------------------------------*/
    $("#SFSaveCatalog").click(function () {
        // Проверить заполнение обязательных полей
        if (!validation.validFill(['SFCatalogName', 'SFCatalogGroupName'])) {
            validation.showError("Не заполнены обязательные поля.");
            return false;
        }

        // обработать данные на сервере
        $('form#SFCatalogForm').submit();
    });

    $("form#SFCatalogForm").submit(function () {
        // Создание записи
        $.ajax({
            type: "GET",
            url: "/engine/components/CatalogAdministrate/action.php?action=create_catalog",
            data: "SFCatalogName=" + $("#SFCatalogName").val() +
                    "&SFCatalogGroupName=" + $("#SFCatalogGroupName").val() +
                    "&SFCalaogComment=" + $("#SFCalaogComment").val(),
            success: function (created) {

                if (created) {
                    location.reload();
                } else
                {
                    validation.showError("Ошибка при создании каталога.");
                }
            }
        });

        return false;
    });

    /* Изменить элемент каталога----------------------------------------------*/
    $(".sf-catalog-item").click(function () {
        catalog_id = $(this).children(".sf-catalog-item-id").val();

        document.location.href = "?action=catalog&id=" + catalog_id;
    });

    /* Кнопка навигации в начало раздела--------------------------------------*/
    $("#SFCatalogNavRoot").click(function () {
        document.location.href = document.location.pathname;
    });
});
