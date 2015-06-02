<?php

class baseException extends Exception {
    public function __construct($message, $file, $line) {
        parent::__construct($message, $code, $previous);
        $this->file = $file;
        $this->line = $line;
    }
}

class dbErrorException extends baseException {
    public function __construct() {
    }
    
}

// Set handlers
set_error_handler(create_function('$c, $m, $f, $l', 'throw new baseException($message, $file, $line);'), E_ALL);
set_error_handler(create_function('$c, $m, $f, $l', 'throw new dbErrorException($message, $file, $line);'), E_ALL);

?>
