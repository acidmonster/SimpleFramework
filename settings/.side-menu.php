<?php

/*
 *  структура пунктов меню:
 *  0 - Заголовок
 *  1 - адрес страницы
 *  2 - Требуется права администратора
 */

$menu = Array(
    Array(
        'Группы каталогов',
        '/settings/cataloggroups/',
        TRUE
    ),
    Array(
        'Каталоги',
        '/settings/catalogs/',
        TRUE
    ),
    Array(
        'Товары',
        '/settings/goods/',
        TRUE
    ),
    Array(
        'Пользователи',
        '/settings/users/',
        TRUE
    )
);
