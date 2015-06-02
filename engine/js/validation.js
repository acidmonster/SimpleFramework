/**
 * Объект для проверки типов данных
 * @type Object
 */
var validation = {};

/**
 * Функция выполняет проверку поля на заполнение данными.
 * @param {Array} element_arr Массив из идентификаторов полей
 */
validation.validFill = function(element_arr) {
    var input_elem;
    var checked = true;
    
    // Если переданное значение является массивом, то обработать его.
    if(element_arr instanceof Array) {
        for(var i = 0; i < element_arr.length; i++) {
            // Получить элемент по идентификатору
            input_elem = $('#' + element_arr[i]);
            
            // Если элемент существует, то выполнить проверку
            if(input_elem.exists()) {
                if(input_elem.val() === "") {
                    input_elem.focus();
                    setErrorStyle(input_elem.parent());
                    checked = false;
                } else {
                    setNormalStyle(input_elem.parent());
                }
            }
        }
    } else {
        checked = false;
    }
    
    return checked;
};

/**
 * Метод для проверки значения на дату
 * @param {Array} element_arr Массив имен полей ввода
 * @returns {Boolean} Результат проверки
 */
validation.validDate = function(element_arr) {
    // Проверить введенное значение на регулярное выражение
    var regTmp = "(0[1-9]|1[0-9]|2[0-9]|3[01]).(0[1-9]|1[012]).[0-9]{4}";
    var checked = true;
    var element;
    
    if(element_arr instanceof Array) {
        for(var i = 0; i < element_arr.length; i++) {
            element = $("#" + element_arr[i]);
    
            if (element.val().search(regTmp) == -1) {
                setErrorStyle(element);
                checked = false;
            } else {
                setNormalStyle(element);
            }
        }
    } else {
        checked = false;
    }
    
    return checked;  
};

/**
 * Метод отображает сообщение об ошибке
 * @param {string} message Текст ошибки
 */
validation.showError = function(message) {
    if(message !== "") {
        // Получить контейнер ошибки
        $(".sf-catalog-error-box").text(message);
        $(".sf-catalog-error-box").show();
    }
};

/**
 * Метод скрывает сообщение об ошибке
 */
validation.hideError = function() {
    $(".sf-catalog-error-box").hide();
    $(".sf-catalog-error-box").text("");
};

/**
 * Метод сравнивает две даты. Если дата first_date раньше даты second_date, 
 * то метод возвращает true, иначе возвращается false
 * @param {Element ID} fDate
 * @param {Element ID} sDate
 * @returns {Boolean}
 */
validation.compareDate = function(fDate, sDate) {
    var fDateElem = $("#" + fDate);
    var sDateElem = $("#" + sDate);
    
    if (fDateElem.exists() && sDateElem.exists()) {
        
        var oFirstDate = dates.getDateFromString(fDateElem.val());
        var oSecondDate = dates.getDateFromString(sDateElem.val());
        
        if (oFirstDate <= oSecondDate) {
            return true;
        }
    }
    
    return false;
};
