<?php

namespace Zend149\Service;

use Zend149\Service\Bitly\Exception;


class Bitly extends \Zend_Service_Abstract
{
    /**
     * Url to the bit.ly API
     */
    const URI_BASE = 'http://api.bit.ly/';

    /**
     * Name for the shorten action
     */
    const ACTION_SHORTEN = 'shorten';

    /**
     * Name for the expand action
     */
    const ACTION_EXPAND = 'expand';

    /**
     * The used bit.ly API key
     *
     * @var string
     */
    protected $_apiKey;
    
    /**
     * The used bit.ly user id
     *
     * @var string
     */
    protected $_login;
    
    /**
     * Format of response
     * 
     * @var string
     */
    protected $_format = 'object';
    
    /**
     * Performs object initializations
     *
     *  # Sets up character encoding
     *  # Saves the user ID
     *  # Saves the API key
     *
     * @param  string $login  Your Bitly user name
     * @param  string $apiKey Your Bitly API key
     * @return void
     */
    public function __construct($login = null, $apiKey = null)
    {
        if (!extension_loaded('iconv')) {
            throw new Exception('Extension "iconv" is not loaded!');
        }
        
        iconv_set_encoding('output_encoding', 'UTF-8');
        iconv_set_encoding('input_encoding', 'UTF-8');
        iconv_set_encoding('internal_encoding', 'UTF-8');
        
        $this->setLogin($login);
        $this->setApiKey($apiKey);
    }
       
    /**
     * Processes the HTTP Request to the API
     *
     * @param string $path Pathname of API method
     * @param array $params Associative array with api method parameters
     * @return Zend_Http_Response HTTP Response Object
     */
    protected function _request($path, array $params = array())
    {
        $client = $this->getHttpClient();
        $client->resetParameters();
        
        // adding base uri
        $client->setUri(self::URI_BASE);
       
        $params = array_merge(array(
            'apiKey' => $this->getApiKey(),
            'login'  => $this->getLogin(),
            'format' => $this->getFormat() == 'object' ? 'json' : $this->getFormat(),
        ), $params);
       
        $client->getUri()->setPath($path);
        $client->setParameterGet($params);

        return $client->request();
    }
    
    /**
     * Wrappes the http response
     * 
     * This method depends on the property <$format>. If this property ist equal
     * to 'object', this method will return an Zend_Service_Bitly_Result object.
     * In other cases, the http response body is returned.
     * 
     * @param Zend_Http_Response $response Response object
     * @param string $action Action name const like self::ACTION_SHORTEN or self::ACTION_EXPAND
     */
    protected function _createResult(\Zend_Http_Response $response, $action)
    {
        $result    = $response->getBody();
        $className = 'Zend149\Service\Bitly\Result\\' . ucfirst($action);
        if ($this->getFormat() == 'object')
        {
            return new $className($result);
        }
        return $result;
    }
       
    /**
     * Shorten an URL by using the API
     *
     * @param string URL to shorten
     * @return Zend149\Service\Bitly\Result\Shorten
     */
    public function shorten($longUrl)
    {
        $response = $this->_request('/v3/shorten', array(
            'longUrl' => $longUrl
        ));
        
        return $this->_createResult($response, self::ACTION_SHORTEN);
    }
       
    /**
     * Given a bit.ly URL or hash (or multiple), this method
     * decodes it by using the API
     *
     * @param string|array $hash One or more hashes or short URLs
     * @return Zend149\Service\Bitly\Result\Expand
     */
    public function expand($hash)
    {
        $params = array();
        // If there is any slash in the string, it should be an url
        if (strpos($hash, '/') !== FALSE) {
            $params['shortUrl'] = $hash;
        } else {
            $params['hash'] = $hash;
        }

        $response = $this->_request('/v3/expand', $params);

        return $this->_createResult($response, self::ACTION_EXPAND);
    }
    
    /**
     * Get the API key
     * 
     * @return string
     */
    public function getApiKey()
    {
        if ($this->_apiKey === null)
        {
            throw new Exception('Api key was not set');
        }
        return $this->_apiKey;
    }
    
    /**
     * Set the API key
     * 
     * @param  string $apikey
     * @return Zend_Service_Bitly
     */
    public function setApiKey($apikey = '')
    {
        $this->_apiKey = $apikey;
        return $this;
    }
    
    /**
     * Get the login name
     * 
     * @return string
     */
    public function getLogin()
    {
        if ($this->_login === null)
        {
            throw new Exception('Login name was not set');
        }
        return $this->_login;
    }
    
    /**
     * Set the login name
     * 
     * @param  string $login Login Name
     * @return Zend_Service_Bitly
     */
    public function setLogin($login = '')
    {
        $this->_login = $login;
        return $this;
    }
    
    /**
     * Get the request format
     * 
     * @return string
     */
    public function getFormat()
    {
        return $this->_format;
    }
    
    /**
     * Set the request format
     * 
     * @param  string $format
     * @return Zend_Service_Bitly
     */
    public function setFormat($format)
    {
        $allowed = array('object', 'json', 'xml', 'txt');
        
        if (!in_array($format, $allowed))
        {
            throw new Exception("Response format '" . $format . "' is not supported");
        }
        $this->_format = $format;
        return $this;
    }
}