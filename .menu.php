<?php

/*
 *  структура пунктов меню:
 *  0 - Заголовок
 *  1 - адрес страницы
 *  2 - Требуется права администратора
 */

$menu = Array(
    Array(
        'Галстуки-бабочки',
        '/',
        FALSE
    ),
    Array(
        'Куклы "Тильда"',
        '/tilda/flowers/',
        FALSE
    ),
    Array(
        'Подарочные коробочки',
        '/2.php',
        FALSE
    ),
    Array(
        'Оплата товара',
        '/3.php',
        FALSE
    ),
    Array(
        'Доставка',
        '/4.php',
        FALSE
    ),
    Array(
        'Настройки',
        '/settings/catalogs',
        TRUE
    )
);
