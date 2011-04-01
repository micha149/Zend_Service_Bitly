<?php

require_once 'Zend/Service/Abstract.php';

class Zend149_Service_Bitly extends Zend_Service_Abstract
{
    /**
     * Url to the bit.ly API
     */
    const URI_BASE = 'http://api.bit.ly';

    /**
     * Name for the shorten action
     */
    const ACTION_SHORTEN = 'shorten';

    /**
     * Name for the expand action
     */
    const ACTION_EXPAND = 'expand';

    /**
     * Name for the clicks action
     */
    const ACTION_CLICKS = 'clicks';

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
        // @codeCoverageIgnoreStart
        if (!extension_loaded('iconv')) {
            throw new Zend_Service_Bitly_Exception('Extension "iconv" is not loaded!');
        }

        iconv_set_encoding('output_encoding', 'UTF-8');
        iconv_set_encoding('input_encoding', 'UTF-8');
        iconv_set_encoding('internal_encoding', 'UTF-8');
        // @codeCoverageIgnoreEnd

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
        //$client->setUri(self::URI_BASE);
       
        $params = array_merge(array(
            'apiKey' => $this->getApiKey(),
            'login'  => $this->getLogin(),
            'format' => $this->getFormat() == 'object' ? 'json' : $this->getFormat(),
        ), $params);
       
        $url = self::URI_BASE.$this->_buildUrl($path, $params);
        $client->setUri($url);

        return $client->request();
    }

    /**
     * Builds GET request
     * 
     * @param string $path url
     * @param array $params get params
     * @return string url with encoded params
     */
    protected function _buildUrl($path, array $params = array())
    {
        $params = $this->_encodeUrl($params);
        if (strpos($path, '?') === FALSE)
            $path .= '?'.$params;
        else
            $path .= '&'.$params;

        return $path;
    }

    /**
     * Encodes GET parameters like `http_build_query` does,
     * needed because bit.ly request format for arrays is
     * a=x&a=y&a=z, not a[]=x&a[]=y&a[]=z
     *
     * !Does not support nested arrays!
     *
     * @param array $params to encode
     * @return string encoded url
     */
    protected function _encodeUrl(array $params)
    {
        $url = '';
        foreach ($params as $key => $value) {
            foreach ((array) $value as $v)
                $url .= '&'.$key.'='.urlencode($v);
        }

        return substr($url, 1);
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
    protected function _createResult(Zend_Http_Response $response, $action)
    {
        $result    = $response->getBody();
        $className = 'Zend149_Service_Bitly_Result_' . ucfirst($action);
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
     * @return Zend149_Service_Bitly_Result_Shorten
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
     * @return Zend149_Service_Bitly_Result_Expand
     */
    public function expand($hash)
    {
        $params = $this->separateHashesFromUrls((array) $hash);

        $response = $this->_request('/v3/expand', $params);

        return $this->_createResult($response, self::ACTION_EXPAND);
    }

    /**
     * Given a list of bit.ly shor URLs or hashes, this method
     * returns overall click statistics on that links.
     *
     * @param array shortUrls list of short urls
     * @return Zend149_Service_Bitly_Result_Clicks
     */
    public function clicks(array $shortUrls)
    {
        //TODO: make appropriate exception classes
        if (count($shortUrls) == 0) {
            throw new Zend149_Service_Bitly_Exception('At least one short url or hash should be passed');
        } else if (count($shortUrls) > 15) {
            throw new Zend149_Service_Bitly_Exception('The maximum number of short urls or hashes is 15');
        }

        $params = $this->separateHashesFromUrls((array) $shortUrls);

        $response = $this->_request('/v3/clicks', $params);

        return $this->_createResult($response, self::ACTION_CLICKS);
    }

    /**
     * Separate array of strings (hashes & urls) to arrays
     * of ['hash' => [hashes], 'shortUrl' => [urls]]
     * 
     * @param array $mixed
     * @return array
     */
    protected function separateHashesFromUrls(array $mixed)
    {
        $result = array();

        foreach ($mixed as $m) {
            if ($this->_isHash($m))
                $result['hash'][] = $m;
            else
                $result['shortUrl'][] = $m;
        }

        return $result;
    }

    /**
     * Checks if provided string is bit.ly hash or url
     *
     * @param string $str
     * @return bool
     */
    protected function _isHash($str) {
        // If there is any slash in the string, it should be an url
        return strpos($str, '/') === FALSE;
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
            throw new Zend149_Service_Bitly_Exception('Api key was not set');
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
            throw new Zend149_Service_Bitly_Exception('Login name was not set');
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
            throw new Zend149_Service_Bitly_Exception("Response format '" . $format . "' is not supported");
        }
        $this->_format = $format;
        return $this;
    }
}