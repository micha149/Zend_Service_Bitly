<?php
class Zend149_Service_Bitly_Result_Shorten extends Zend149_Service_Bitly_Result
{
    
    protected $_newHash;

    protected $_url;

    protected $_hash;

    protected $_globalHash;

    protected $_longUrl;

    public function isNewHash()
    {
        return $this->_newHash;
    }

    public function getNewHash()
    {
        return $this->isNewHash();
    }

    public function setNewHash($_newHash)
    {
        $this->_newHash = (bool) $_newHash;
        return $this;
    }

    public function getUrl()
    {
        return $this->_url;
    }

    public function setUrl($_url)
    {
        $this->_url = $_url;
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

    public function getGlobalHash()
    {
        return $this->_globalHash;
    }

    public function setGlobalHash($_globalHash)
    {
        $this->_globalHash = $_globalHash;
        return $this;
    }

    public function getLongUrl()
    {
        return $this->_longUrl;
    }

    public function setLongUrl($_longUrl)
    {
        $this->_longUrl = $_longUrl;
        return $this;
    }

}