/* global validation */

/**
 * Ожидаем загрузку документа
 */

$(document).ready(function () {
    /* Открыть форму группы---------------------------------------------------*/
    $("#SFAddGroup").click(function () {
        $(".sh-catalog-frame").css("display", "block");
        $(".sh-catalog-group-form").animate({
            left: "+=600"
        }, 300, function () {
            $("#SFGroupName").focus();
        });
    });


    /* Закрыть форму группы---------------------------------------------------*/
    $("#SFCancelGroup").click(function () {
        $(".sh-catalog-group-form").animate({
            left: "-=600"
        }, 300, function () {
            setNormalStyle($("#SFGroupName").parent());
            $(".sh-catalog-frame").css("display", "none");
            $("#SFAddGroupForm")[0].reset();
            $(".sf-catalog-error-box").hide();
            $(".sf-catalog-error-box").text("");
        });
    });

    /* Проверка заполнения группы --------------------------------------------*/
    $("#SFGroupName").change(function () {
        element = $("#SFGroupName");

        $.ajax({
            type: "GET",
            url: "/engine/components/CatalogAdministrate/create.php?action=check_group",
            data: "SFGroupName=" + element.val(),
            success: function (checked) {
                if (checked) {
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

    /**
     * Отправка данных на сервер
     */
    $("#SFCreateGroup").click(function () {
        // Проверить заполнение обязательных полей
        if (!validation.validFill(['SFGroupName'])) {
            validation.showError("Не заполнены обязательные поля.");
            return false;
        }

        // обработать данные на сервере
        $('form#SFAddGroupForm').submit();
    });

    $("form#SFAddGroupForm").submit(function () {
        // Создание записи
        $.ajax({
            type: "GET",
            url: "/engine/components/CatalogAdministrate/create.php?action=create_group",
            data: "groupname=" + $("#SFGroupName").val() + "&SFGroupComment=" + $("#SFGroupComment").val(),
            success: function (created) {
                alert(created);
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
});