<!DOCTYPE html>

<!--
  Copyright (c) Антон калашников, 2015
-->

<html>
    <head>
        <meta charset="utf-8">
        <link rel="shortcut icon" href="/images/icon.ico">
        <link rel="stylesheet" type="text/css" href="<?php echo CApp::APP_TEMPLATES_PATH?>./shop/style.css" />
        <script type="text/javascript" src="/engine/js/jquery-2.1.1.min.js"></script>
        <script type="text/javascript" src="/engine/js/general.js"></script>
        <title><?php echo $page_data->getTitle() ?></title>
    </head>
    <body>
        <header class="sh-header-area">
            <div class="sh-header-content"></div>
            <nav class="sh-menu-area">
                <div class="wrap">
                    <?php echo CApp::loadComponent("Menu"); ?>
                </div>
            </nav>
        </header>

        <section class="sh-content-area">
            <div class="sh-page-title wrap"><h1><?php echo $page_data->getTitle() ?></h1></div>
            <div class="sh-content wrap">
                <aside class="sh-vertical-menu">
                    <?php echo CApp::loadComponent("SideMenu"); ?>
                </aside>
                <div class="sh-content-side"><?php echo $page_data->getContent(); ?></div>
            </div>
        </section>
        <footer class="sh-footer-area">
            <div></div>
        </footer>
    </body>
</html>
