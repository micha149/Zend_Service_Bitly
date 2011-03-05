<?php
//TODO: create wrapper for every click result
class Zend149_Service_Bitly_Result_Clicks extends Zend149_Service_Bitly_Result
{
    protected $_clicks = array();
    protected $_clicksByUrl = array();
    protected $_clicksByHash = array();

    public function setClicks(array $clicks)
    {
        $this->_clicks = $clicks;
    
        foreach ($this->_clicks as &$c) {
            if (isset($c['short_url'])) {
                $this->_clicksByUrl[$c['short_url']] =& $c;
                $this->_clicksByHash[$c['user_hash']] =& $c;
            }

            if (isset($c['hash'])) {
                $this->_clicksByHash[$c['hash']] =& $c;
            }
        }

        return $this;
    }

    public function getClicks()
    {
        return $this->_clicks;
    }

    //TODO: thorw exception if item not found
    public function getByUrl($url) 
    {
        return $this->_clicksByUrl[$url];
    }

    //TODO: thorw exception if item not found
    public function getByHash($hash)
    {
        return $this->_clicksByHash[$hash];
    }
};
