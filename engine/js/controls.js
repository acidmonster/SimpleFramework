/* 
 * ------------------------------------------
 *          События лементов управления
 * ------------------------------------------
 */

function setErrorStyle(element) {
    element.css("border", "1px solid #cd0a0a");
}

function setNormalStyle(element) {
    element.css("border", "1px solid #999");
}

/**
 * Функция перенаправляет браузер на страницу переданную в параметре page
 * @param {url} page страница для перехода
 */
function setPage(page) {
    if (page) {
        document.location.replace(page);
    }
}
;

// ---------onMouseClick
$("#pmExitButton").click(function () {
    document.location.replace("/authorize.php?action=exit");
});
