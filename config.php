<?php
class Config{
    private $dns = 'mysql:host=localhost;dbname=ChainOfStores';
    private $user = 'root';
    private $pass = '1998nick';

    /**
     * @return string
     */
    public function getDns()
    {
        return $this->dns;
    }

    /**
     * @return string
     */
    public function getUser()
    {
        return $this->user;
    }

    /**
     * @return string
     */
    public function getPass()
    {
        return $this->pass;
    }

}
?>