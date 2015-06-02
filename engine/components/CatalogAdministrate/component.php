<?php

/* ===========================================================================
 * Компонент: Каталог. Администрирование
 * Описание: Компонент позволяет производить действия с каталогами.
 * ===========================================================================
 */

class CCatalogAdministrate {

    const SCRIPT_FILE_NAME = "script.js";
    const STYLE_FILE_NAME = "style.css";
    const TABLE_NAME = "sf_catalogs_groups";

    protected $html;

    public function __construct() {
        $this->html .= $this->readScript();
        $this->html .= $this->readCSS();
    }

    public function __destruct() {

    }

    /**
     * Метод читает файл style.css и возвращает содержимое в виде строки
     * @return string Содержимое файла style.css
     */
    protected function readScript() {
        // Проверить наличие файла скриптов
        $data = "";

        if (file_exists(__DIR__ . "/".self::SCRIPT_FILE_NAME)) {
            $f = fopen(__DIR__ . "/".self::SCRIPT_FILE_NAME, "r");

            // Читать построчно до конца файла
            while (!feof($f)) {
                $data .= fgets($f) . "\n";
            }

            fclose($f);
        }

        return "<script>" . $data . "</script>";
    }

    /**
     * Метод читает файл style.css и возвращает содержимое в виде строки
     * @return string Содержимое файла style.css
     */
    protected function readCSS() {
      // Проверить наличие таблицы стилей
        $data = "";

        if (file_exists(__DIR__ . "/".self::STYLE_FILE_NAME)) {
            $f = fopen(__DIR__ . "/".self::STYLE_FILE_NAME, "r");

            // Читать построчно до конца файла
            while (!feof($f)) {
                $data .= fgets($f) . "\n";
            }

            fclose($f);
        }

        return "<style>" . $data . "</style>";
    }

    /**
     *
     * @param array $param_array  Массив параметров в формате "Имя параметра" => "Значение параметра"
     * @return string Djpdhfoftn HTML код компонента
     */
    public function render($param_array = []) {
        /* Работа с каталогами допускается только администраторам системы,
         * поэтому выполняем проверку авторизации
         */

        if (Capp::checkAuthorize()) {
            // Отобразить шаблон компонента
            $this->html .=
                    '<div class="sf-catalog-settings-area">
                        <div class="sf-catalog-setting-buttons">
                            <div class="sf-image-button" id="SFAddGroup"><div class="sf-image-button-icon add-catalog-group"></div><a href="#">Добавить группу</a></div>
                            <div class="sf-image-button" id="SFAddCatalog"><div class="sf-image-button-icon add-catalog-item"></div><a href="#">Добавить каталог</a></div>
                        </div>';


            // Получить группы из базы данных
            $con    = new CConnection();
            $result = $con->query("select id, name from " . self::TABLE_NAME . " where state = 'E' order by name");

            if ($result->num_rows > 0) {

            } else {
                $this->html .= '<div class="sf-catalog-group-not-found"><p><b>Пока нет ни одной группы.</b></p><p></p>Для добавления новой группы каталогов нажмите кнопку "Добавить группу".</div>';
            }

            // Форма добавления новой группы
            $form_add_group = '<form id="SFAddGroupForm" action="?action=add_group" method="post">
            <div class="sh-catalog-frame">
                <div class="sh-catalog-group-form"><h2>Добавление новой группы</h2>
                    <input type="hidden" id="SFCatalogAdminAction" value="AddGroup">
                    <div class="sf-catalog-error-box"></div>
                    <div class="sf-catalog-label">*Наименование</div>
                     <div class="sf-input">
                        <input type="text" alt="Наименование группы" name="SFGroupName" id="SFGroupName" maxlength="50" value="">
                    </div>
                    <div class="sf-catalog-label">Примечание</div>
                    <div class="sf-input">
                        <textarea alt="Примечание" name="SFGroupComment" id="SFGroupComment" maxlength="200"></textarea>
                    </div>
                    <div class="sf-catalog-footer">
                        <div class="sf-image-button" id="SFCreateGroup"><div class="sf-image-button-icon save-catalog-item"></div><a href="#">Сохранить</a></div>
                        <div class="sf-image-button" id="SFCancelGroup"><div class="sf-image-button-icon cancel-catalog-item"></div><a href="#">Отмена</a></div>
                    </div>
                </div>
            </div>
        </form>';

            $this->html .= $form_add_group;
            $this->html .= '</div>';

            return $this->html;
        } else {
            return "";
        }
    }

}
