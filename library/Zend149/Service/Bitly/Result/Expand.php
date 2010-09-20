<?php

namespace Zend149\Service\Bitly\Result;

use Zend149\Service\Bitly\Result;

class Expand extends Result
{

    public function __construct($result)
    {
        $resultObject = \Zend_Json::decode($result);
        $this->setStatusCode($resultObject['status_code'])
             ->setStatusText($resultObject['status_txt'])
             ->setData($resultObject['data']['expand'][0]);
    }

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