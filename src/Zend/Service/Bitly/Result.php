<?php 

class Zend_Service_Bitly_Result
{
    protected $_data;
    
    protected $_statusCode;
    
    protected $_statusText;
    
    public function __construct(Zend_Http_Response $response)
    {
        $responseBody = json_decode($response->getBody());
        
        $this->setStatusCode($responseBody->status_code)
             ->setStatusText($responseBody->status_txt)
             ->setData($responseBody->data);
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
 
    protected function setData(stdClass $data)
    {
        $methods = get_class_methods($this);
        foreach ($data as $key => $value) {
            $key = $this->parseKeyName($key);
            $this->_data[$key] = $value;
        }
        return $this;
    }
    
    protected function parseKeyName($key)
    {
        $parts = explode('_', $key);
        for ($i = 1; $i < count($parts); $i++) {
            $parts[$i] = ucfirst($parts[$i]);
        }
        return implode($parts);
    }
}