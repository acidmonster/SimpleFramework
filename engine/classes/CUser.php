<?php

include_once 'CObject.php';

class CUser extends CObject {

    /**
     * User email
     * @var string type
     */
    protected $email;

    /**
     * User first_name
     * @var string type
     */
    protected $first_name;

    /**
     * User second_name
     * @var string type
     */
    protected $second_name;

    /**
     * User last_name
     * @var string type
     */
    protected $last_name;

    /**
     * User account state. Can take values: 'e' - enabled, 'd' - disabled.
     * @var char type
     */
    protected $state;

    /**
     * User account type. Can take values: 's' - system, 'n' - normal.
     * @var char type
     */
    protected $type;

    /**
     * User account date of the registration.
     * @var date type
     */
    protected $reg_date;

    /**
     * User account last sign date.
     * @var date type
     */
    protected $last_sign_date;

    /**
     * Account comment;
     * @var string
     */
    protected $comment;

    /**
     * User birthdate
     * @var date
     */
    protected $birthdate;

    /**
     * Логин пользователя
     * @var string
     */
    protected $login;

    /**
     *
     * @method string getId(void)
     * @method string getEmail(void)
     * @method string getLastName(void)
     * @method string getSecondName(void)
     * @method string getFirstName(void)
     * @method string getState(void)
     * @method string getType(void)
     * @method string getRegDate(void)
     * @method string getLastSignDate(void)
     * @method string getComment(void)
     * @method string getBirthdate(void)
     * @method string getLogin(void)
     * @method string setId($value)
     * @method string setEmail($value)
     * @method string setLastName($value)
     * @method string setSecondName($value)
     * @method string setFirstName($value)
     * @method string setState($value)
     * @method string setType($value)
     * @method string setRegDate($value)
     * @method string setLastSignDate($value)
     * @method string setComment($value)
     * @method string setBirthdate($value)
     * @method string setLogin($value)
     */
    public function __call($method_name, $arguments) {
        $args = preg_split('/(?<=\w)(?=[A-Z])/', $method_name);
        $action = array_shift($args);
        $property_name = strtolower(implode('_', $args));

        switch ($action) {
            case 'get':
                return isset($this->$property_name) ? $this->$property_name : null;

            case 'set':
                $this->$property_name = $arguments[0];
                return $this;
        }
    }

}