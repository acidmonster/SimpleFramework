$(document).ready(function (){
    console.log("Инициализация.");
    // Инициализация события "Submit" формы
    $("form#SFAuthorizeForm").submit(function () {
        console.log("Данные отправлены.");
        
        return  true;
    });
    
    $("#SFAuthorizeButton").click(function () {
        // Отправить данные на сервер.
        $("form#SFAuthorizeForm").submit();
    });
});