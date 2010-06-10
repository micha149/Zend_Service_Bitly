<?php

require_once 'Zend/Service/Abstract.php';

class Zend_Service_Bitly extends Zend_Service_Abstract
{
    /**
     * Url to the bit.ly API
     */
    const URI_BASE = 'http://api.bit.ly/';

    /**
     * The used bit.ly API key
     *
     * @var string
     */
    public $apiKey;
    
    /**
     * The used bit.ly user id
     *
     * @var string
     */
    public $userId;
    
    /**
     * Format of response
     *
     *
     */
    public $format = 'object';
    
    /**
     * Reference to HTTP client object
     *
     * @var Zend_Http_Client
     */
    protected $_httpClient;
    
    /**
     * Performs object initializations
     *
     *  # Sets up character encoding
     *  # Saves the user ID
     *  # Saves the API key
     *
     * @param  string $apiKey Your Flickr API key
     * @return void
     */
    public function __construct($userId = '', $apiKey = '')
    {
        if (!extension_loaded('iconv')) {
            throw new Zend_Service_Bitly_Exception('Extension "iconv" is not loaded!');
        }
        
        iconv_set_encoding('output_encoding', 'UTF-8');
        iconv_set_encoding('input_encoding', 'UTF-8');
        iconv_set_encoding('internal_encoding', 'UTF-8');
        $this->userId = (string) $userId;
        $this->apiKey = (string) $apiKey;
    }
    
    /**
     * Returns a reference to the HTTP client, instantiating it if necessary
     *
     * @return Zend_Http_Client
     */
    public function getHttpClient()
    {
        if (null === $this->_restClient) {
            require_once 'Zend/Http/Client.php';
            $this->setHttpClient(new Zend_Http_Client(self::URI_BASE));
        }
        return $this->_httpClient;
    }
    
    /**
     * Sets the used http client
     *
     * @param Zend_Http_Client $client Http client object
     * @return Zend_Service_Bitly Returns itself for fluent concatination
     */
    public function setHttpClient(Zend_Http_Client $client)
    {
        $this->_httpClient = $client;
        return $this;
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
       
        $params = array_merge(array(
            'apiKey' => $this->apiKey,
            'login'  => $this->userId,
            'format' => $this->format == 'object' ? 'json' : $this->format
        ), $params);
       
        $client->getUri()->setPath($path);
        $client->setParameterGet($params);

        return $client->request(Zend_Http_Client::GET);
    }
    
    /**
     * Wrappes the http response
     * 
     * This method depends on the property <$format>. If this property ist equal
     * to 'object', this method will return an Zend_Service_Bitly_Result object.
     * In other cases, the http response body is returned.
     * 
     * @param Zend_Http_Response $response Response object
     */
    protected function _createResult(Zend_Http_Response $response)
    {
        if ($this->format == 'object') {
            return new Zend_Service_Bitly_Result($response);
        }
        return $response->getBody();
    }
       
    /**
     * Shorten an URL by using the API
     *
     * @param string URL to shorten
     */
    public function shorten($longUrl)
    {
        $response = $this->_request('/v3/shorten', array(
            'longUrl' => $longUrl
        ));
        
        return $this->_createResult($response);
    }
       
    /**
     * Given a bit.ly URL or hash (or multiple), this method
     * decodes it by using the API
     *
     * @param string|array $hash One or more hashes or short URLs
     * @return 
     */
    public function expand($hash)
    {
        $params = array();
        // If there is any slash in the string, it should be an url
        if (strpos($hash, '/') !== FALSE) {
            $params['shortUri'] = $hash;
        } else {
            $params['hash'] = $hash;
        }

        $response = $this->_request('/v3/expand', $params);
        return new $this->_createResult($response);
    }
}