// Директива жесткого контроля JavaScript
"use strict";

/**
 * Прототип функции для проверки наличия элемента
 * @returns {$.length|jQuery.length}
 */
jQuery.fn.exists = function() {
    return $(this).length;
};

var app = {};

$(document).ready(function (){
    app.initialize();
});

app.initialize = function() {
    this.include('/engine/js/formats.js');
    this.include('/engine/js/validation.js');
    this.include('/engine/js/controls.js');
};

// load all  script
app.include = function(url) {
    var script = document.createElement('script');
    script.src = url;
    document.getElementsByTagName('head')[0].appendChild(script);
};