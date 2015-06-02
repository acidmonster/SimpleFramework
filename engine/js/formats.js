var dates = {};

/**
 * Метод по переданному строковому значению даты 
 * возвращает соответсвующий объект с типом Date.
 * @param {string} date Строковое значение даты
 * @returns {Date}
 */
dates.getDateFromString = function(date) {
    if (date === "") {
        return null;
    }
    
    var arrDate = date.split(".");
    var oDate = new Date(arrDate[2], arrDate[1] - 1, arrDate[0]);
    
    return oDate;
};

/**
 * Метод смещает дату на указанный интервал и по указанной части.
 * @param {Date} date Дата, которую необходимо изменить.
 * @param {string} part Часть, относительно которой смещается дата. 
 * Допустимые значения: 'd' - день,
 *                      'm' - месяц,
 *                      'y' - год.
 * @param {integer} value Число, на которое нужно изменить дату.
 * @returns {Date}
 */
dates.changeDate = function(date, part, value) {
    if (date instanceof Date && $.isNumeric(value) && part !== "")
    {
        var arrDate = [];
        arrDate[0] = date.getDate();
        arrDate[1] = date.getMonth();
        arrDate[2] = date.getFullYear();
        
        switch (part) {
            case "d":
                arrDate[0] += value;
                break;
                
            case "m":
                arrDate[1] += value;
                break;
                
            case "y":
                arrDate[2] += value;
                break;
            default : 
                return null;
        }
        
        var chgDate = new Date(arrDate[2], arrDate[1], arrDate[0]);
        
        return chgDate;
    } else {
        return null;
    }
};