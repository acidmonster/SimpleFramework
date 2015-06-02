/* global validation */

/**
 * Ожидаем загрузку документа
 */

$(document).ready(function () {

    /* =========================================================================
     * События формы групп каталогов
     * ========================================================================= 
     */
    
    /* Открыть форму группы---------------------------------------------------*/
    $("#SFAddGroup").click(function () {
        $("#SFGroupForm").show();
        $(".sf-catalog-background").show();
    });


    /* Закрыть форму группы---------------------------------------------------*/
    $("#SFCancelGroup").click(function () {
        $(".sf-catalog-background").hide();
        $("#SFGroupForm").hide();
        $("#SFGroupForm")[0].reset();
        setNormalStyle($("#SFGroupName").parent());
        $(".sf-catalog-error-box").hide();
        $(".sf-catalog-error-box").text("");
    });
    
    /* Проверка заполнения группы --------------------------------------------*/
    $("#SFGroupName").change(function () {
        element = $("#SFGroupName");

        $.ajax({
            type: "GET",
            url: "/engine/components/CatalogAdministrate/action.php?action=check_group",
            data: "SFGroupName=" + element.val(),
            success: function (checked) {
                if (checked == true) {
                    setNormalStyle(element.parent());
                    validation.hideError();
                } else
                {
                    element.focus();
                    setErrorStyle(element.parent());
                    validation.showError("Группа с таким наименованием уже существет.");
                }
            }
        });
    });

    /* Отправка данных на сервер ---------------------------------------------*/
    $("#SFSaveGroup").click(function () {
        // Проверить заполнение обязательных полей
        if (!validation.validFill(['SFGroupName'])) {
            validation.showError("Не заполнены обязательные поля.");
            return false;
        }

        // обработать данные на сервере
        $('form#SFGroupForm').submit();
    });

    $("form#SFGroupForm").submit(function () {
        // Создание записи
        $.ajax({
            type: "GET",
            url: "/engine/components/CatalogAdministrate/action.php?action=create_group",
            data: "SFGroupName=" + $("#SFGroupName").val() + "&SFGroupComment=" + $("#SFGroupComment").val(),
            success: function (created) {

                if (created) {
                    location.reload();
                } else
                {
                    validation.showError("Ошибка при создании группы.");
                }
            }
        });

        return false;
    });
    
    /* Изменить элемент группы -----------------------------------------------*/
    $(".sf-catalog-group-item").click(function() {
        groupID = $(".sf-catalog-group-item").children(".sf-catalog-group-item-id").val();
        
        $.ajax({
            type: "GET",
            url: "/engine/components/CatalogAdministrate/action.php?action=get_group",
            data: "SFGroupID" + groupID,
            success: function (js) {

                if (js !== "error") {
                    
                    
                    alert("fsdfsdfsdfdddddddd");
                }                
            }
        });
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
        if (!validation.validFill(['SFCatalogName','SFCatalogGroupName'])) {
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
            data:   "SFCatalogName=" + $("#SFCatalogName").val() + 
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
    $(".sf-catalog-item").click(function() {
        catalog_id = $(".sf-catalog-item").children(".sf-catalog-item-id").val();
        
        document.location.href = "?action=catalog&id=" + catalog_id;        
    });
    
    /* Кнопка навигации в начало раздела--------------------------------------*/
    $("#SFCatalogNavRoot").click(function() {        
        document.location.href = document.location.pathname;        
    });
});