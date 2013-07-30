<?php
class Urfa_Auth_Storage implements Zend_Auth_Storage_Interface
{
    /**
     * See if the session is empty
     *
     * @return boolean
     */
    public function isEmpty()
    {
        return empty($_SESSION['utm5']['user']);
    }

    /**
     * Read the contents from the session
     *
     * @return null|User
     */
    public function read()
    {
        $contents = NULL;

        if (!empty($_SESSION['utm5']['user'])) {
            $contents = $_SESSION['utm5']['user'];
        }

        return $contents;
    }

    /**
     * Store content in the session
     *
     * @param User $contents
     */
    public function write($contents)
    {
        if (!($contents instanceof Urfa_Client)) {
            $contents = NULL;
        }

        $_SESSION['utm5']['user'] = $contents;
    }

    /**
     * Clear the data stored in the session
     *
     */
    public function clear()
    {
        unset($_SESSION['utm5']['user']);
    }
}