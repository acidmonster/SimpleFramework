<?php

class CForms {

    // Типы сообщений
    const INFORMATION_CAPTION = 'INFORMATION_CAPTION';
    const CONFIRM_CAPTION     = 'CONFIRM_CAPTION';
    const WARNING_CAPTION     = 'WARNING_CAPTION';
    const ERROR_CAPTION       = 'ERROR_CAPTION';
    // Типы кнопок
    const BUTTON_OK           = 'BUTTON_OK';
    const BUTTON_CANCEL       = 'BUTTON_CANCEL';
    const BUTTON_ADD          = 'BUTTON_ADD';
    const BUTTON_DELETE       = 'BUTTON_DELETE';

    /**
     * Метод возвращает наименование класса иконки кнопки по наименвоанию типа
     * @param string $button_type Наименование типа кнопки
     * @return string наименвоание CSS-класса
     */
    public static function getButtonStyle($button_type) {
        $style = "";
        switch ($button_type) {
            case self::BUTTON_OK:
                $style = "sf-icon-ok";
                break;

            case self::BUTTON_CANCEL:
                $style = "sf-icon-cancel";
                break;

            case self::BUTTON_ADD:
                $style = "sf-icon-add";
                break;

            case self::BUTTON_DELETE:
                $style = "sf-icon-delete";
                break;

            default:
                $style = "";
                break;
        }

        return $style;
    }

    /**
     * Метод возвращает форму информационного сообщения
     * @param string $form_id Идентификатор формы. Используется в скриптах JS
     * @param string $title Заголовок формы
     * @param string $message Сообщение формы
     * @param string $buttons Описание кнопок формы
     * @return string HTML-код формы
     */
    public static function message_box($form_id, $title, $message, $buttons, $width="400", $height="200") {
        $icon = "";

        switch ($title) {
            case self::INFORMATION_CAPTION:
                $title = 'Информация';
                $icon  = '<div class="sf-msbox-icon sf-icon-info"></div>';

                break;

            case self::CONFIRM_CAPTION:
                $title = 'Подтверждение';
                $icon  = '<div class="sf-msbox-icon sf-icon-confirm"></div>';
                break;

            case self::WARNING_CAPTION:
                $title = 'Предупреждение';
                $icon  = '<div class="sf-msbox-icon sf-icon-warning"></div>';
                break;

            case self::ERROR_CAPTION:
                $title = 'Ошибка';
                $icon  = '<div class="sf-msbox-icon sf-icon-error"></div>';
                break;

            default:
                $icon = "";
                break;
        }

        // Сформировать кнопки диалога
        $buttons_html  = '<div class="sf-catalog-footer">';
        $buttons_array = explode("|", $buttons);

        foreach ($buttons_array as $button_item) {
            $button_data = explode(":", $button_item);

            $button_type    = $button_data[0];
            $button_id      = $button_data[1];
            $button_caption = $button_data[2];
            $button_style   = self::getButtonStyle($button_type);

            $buttons_html .= '<div class="sf-image-button" id="' . $button_id . '">'
                    . '<div class="sf-image-button-icon ' . $button_style . '"></div>'
                    . '<a href="#">' . $button_caption . '</a>'
                    . '</div>';
        }

        $buttons_html .= '</div>';

        $form = '<div class="sf-form-background"><div class="sf-form-background-grey"></div>
                <form id="' . $form_id . '" action="" method="post" style="width: '.$width.'px;height:'.$height.'px;">
                <div class="sh-form-frame">
                    <div class="sh-form-panel"><h2>' . $title . '</h2>
                        <div class="sf-form-message-panel">' . $icon . '<div style="float:left; padding: 5px 0 0 0">' . $message . '</div></div>
                        ' . $buttons_html . '
                    </div>
                </div>
                </form>
                </div>';

        return $form;
    }

}
