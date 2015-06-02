<?php
// Если вызывается шаблон без инициации, то редерикт на главную страницу
if (!isset($page_data)) {
    $path = 'Location: /';
    header($path);
}

?>

<html>
    <head>
        <meta charset="utf-8">
        <link rel="shortcut icon" href="/images/icon.ico">
        <link rel="stylesheet" type="text/css" href="style.css" />
        <script type="text/javascript" src="/engine/js/jquery-2.1.1.min.js"></script>
        <script type="text/javascript" src="script.js"></script>
        <title><?php echo $page_data->getTitle() ?></title>
    </head>
    <body>
        <form id="SFAuthorizeForm" action="?action=login" method="post">
            <section class="sf-authorize-area">
                <input type="hidden" id="SFAuthorizeAction" value="login">
                <div class="sf-authorize-error-box" style="display: <?php echo $display_error;?>"><?php echo $error_message; ?></div>
                <div class="sf-authorize-label">Логин</div>
                <div class="sf-authorize-input">
                    <input type="text" alt="Логин" name="SFAuthorizeLogin" id="SFAuthorizeLogin" maxlength="50" value="<?php echo $login; ?>">
                </div>
                <div class="sf-authorize-label">Пароль</div>
                <div class="sf-authorize-input">
                    <input type="password" alt="Пароль" name="SFAuthorizePassword" id="SFAuthorizePassword" maxlength="50">
                </div>
                <div class="sf-authorize-remeber">
                    <div class="sf-authorize-remeber-check"><input type="checkbox" name="SFAuthorizeRemeber" id="SFAuthorizeRemeber" <?php echo $remember; ?>></div><div class="sf-authorize-remeber-label">Запомнить меня на этом компьютере</div>
                </div>
                <div class="sf-authorize-footer">
                    <div class="sf-authorize-forget"><a href="/" class="sf-authorize-forget-link">Забыли пароль?</a></div>
                    <div class="sf-authorize-button"><input type="button" id="SFAuthorizeButton" value="Войти"></div>
                </div>
            </section>
        </form>
    </body>
</html>

