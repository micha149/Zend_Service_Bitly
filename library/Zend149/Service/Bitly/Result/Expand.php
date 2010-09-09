<?php
class Zend149_Service_Bitly_Result_Expand extends Zend149_Service_Bitly_Result
{

    protected $_shortUrl;

    protected $_hash;

    protected $_userHash;

    protected $_globalHash;

    protected $_error;

    protected $_longUrl;

    public function getShortUrl()
    {
        return $this->_shortUrl;
    }

    public function setShortUrl($_shortUrl)
    {
        $this->_shortUrl = $_shortUrl;
        return $this;
    }

    public function getHash()
    {
        return $this->_hash;
    }

    public function setHash($_hash)
    {
        $this->_hash = $_hash;
        return $this;
    }

    public function getUserHash()
    {
        return $this->_userHash;
    }

    public function setUserHash($_userHash)
    {
        $this->_userHash = $_userHash;
        return $this;
    }

    public function getGlobalHash()
    {
        return $this->_globalHash;
    }

    public function setGlobalHash($_globalHash)
    {
        $this->_globalHash = $_globalHash;
        return $this;
    }

    public function getError()
    {
        return $this->_error;
    }

    public function setError($_error)
    {
        $this->_error = $_error;
        return $this;
    }

    public function getLongUrl()
    {
        return $this->_longUrl;
    }

    public function setLongUrl($_longUrl)
    {
        $this->_longUrl = $_longUrl;
    }

}