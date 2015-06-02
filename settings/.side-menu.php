<?php

/*
 *  структура пунктов меню:
 *  0 - Заголовок
 *  1 - адрес страницы
 *  2 - Требуется права администратора
 */

$menu = Array(
    Array(
        'Каталоги',
        '/settings/catalogs/',
        TRUE
    ),
    Array(
        'Пользователи',
        '/settings/users/',
        TRUE
    )
);
