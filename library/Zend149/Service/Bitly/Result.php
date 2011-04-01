<?php 

abstract class Zend149_Service_Bitly_Result
{
    protected $_statusCode;
    
    protected $_statusText;
    
    public function __construct($result)
    {
        $resultObject = Zend_Json::decode($result);
        $this->setStatusCode($resultObject['status_code'])
             ->setStatusText($resultObject['status_txt'])
             ->setData($resultObject['data']);
    }
    
    public function setStatusCode($code)
    {
        $this->_statusCode = (int) $code;
        return $this;
    }
    
    public function getStatusCode()
    {
        return $this->_statusCode;
    }
    
    public function setStatusText($text)
    {
        $this->_statusText = (string) $text;
        return $this;
    }
    
    public function getStatusText()
    {
        return $this->_statusText;
    }
 
    public function setData(array $data)
    {
        $methods = get_class_methods($this);
        foreach ($data as $key => $value)
        {
            $key    = $this->_parseKeyName($key);
            $method = 'set' . ucfirst($key);
            if (in_array($method, $methods))
            {
                $this->$method($value);
	        }
        }
        return $this;
    }

    protected function _parseKeyName($key)
    {
        $parts = explode('_', $key);
        for ($i = 1; $i < count($parts); $i++) {
            $parts[$i] = ucfirst($parts[$i]);
        }
        return implode($parts);
    }
}