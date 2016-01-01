<?php
class Urfa_Auth_Adapter implements Zend_Auth_Adapter_Interface
{
    /**
     * Username
     *
     * @var string
     */
    protected $username = NULL;

    /**
     * Password
     *
     * @var string
     */
    protected $password = NULL;

    /**
     * Class constructor
     *
     * The constructor sets the username and password
     *
     * @param string $username
     * @param string $password
     */
    public function __construct($username, $password)
    {
        $this->username = $username;
        $this->password = $password;
    }

    /**
     * Authenticate
     *
     * Authenticate the username and password
     *
     * @return Zend_Auth_Result
     */
    public function authenticate()
    {
        // Try to fetch the user from the database using the model
        $users = new Urfa_Client();

        $user = $users->login($this->username, $this->password, false);

       // Zend_Debug::dump($user);

        $user_identity['login'] = $this->username;
        $user_identity['utm5'] = $user['utm5'];
        // Initialize return values
        $code = Zend_Auth_Result::FAILURE;
        $identity = NULL;
        $messages = array();

        // Do we have a valid user?
        if (!$user === FALSE) {
            $code = Zend_Auth_Result::SUCCESS;
            $identity = (object)$user_identity;
        } else {
            $messages[] = 'Authentication error';
        }

        return new Zend_Auth_Result($code, $identity, $messages);
    }
}