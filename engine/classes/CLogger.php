<?php

/**
 * Description of CLogger
 *
 * @author family
 */
class CLogger {
    /**
     *
     * @param type $error_message Write message to file
     */
    public static function writeLog($error_message) {
        $file = fopen(CApp::GetRootFolder()."".CApp::APP_LOG_FILE_PATH, 'a');
        $new_line = chr(10);

        if ($file) {
            fwrite($file, date('d.m.Y H:i:s').' '.$error_message."".$new_line);
        }

        fclose($file);
    }
}