<?php

// there are many ways in PHP to load remote HTTP file, we'll do curl
class Varien_Convert_Adapter_Http_Curl extends Varien_Convert_Adapter_Abstract
{
    // load method
    public function load()
    {
        // we expect <var name="uri">http://...</var>
        $uri = $this->getVar('uri');

        // validate input parameter
        if (!Zend_Uri::check($uri)) {
            $this->addException("Expecting a valid 'uri' parameter");
        }

        // use Varien curl adapter
        $http = new Varien_Http_Adapter_Curl;

        // send GET request
        $http->write('GET', $uri);

        // read the remote file
        $data = $http->read();

        // save contents into container
        $this->setData($data);

        return $this;
    }

    public function save()
    {
        // no save implemented
        return $this;
    }
}