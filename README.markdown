Zend service component for bit.ly shortening service
====================================================

Zend149_Service_Bitly is an additional component for the zend framework. It allows you to use the bit.ly shortening service thourgh a php class.

    $bitly = new Zend149_Service_Bitly($yourLogin, $yourApiKey);
    $shortUrl = $bitly->shorten('http://example.com');
    echo $shortUrl->getUrl(); // will print http://bit.ly/atA9Mk

It is also possible to extract a previously shortened URL:

    $longUrl = $bitly->expand('http://bit.ly/atA9Mk');
    echo $longUrl->getLongUrl(); // will print http://example.com