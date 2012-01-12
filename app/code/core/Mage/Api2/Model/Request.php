<?php


/**
 *  List all magic query params used for special purposes:
 *  - key - OAuth access key
 *  - type - resource type
 *  - model - resource model
 *
 *  List headers:
 *  - Authorization
 *  - Version
 */
class Mage_Api2_Model_Request extends Zend_Controller_Request_Http
{
    const BASE_URL = '/api/:api/';
    
    public function __construct()
    {
        $replace = array(':api' => $this->getApiType('rest'));
        $baseUrl = strtr(self::BASE_URL, $replace);
        $this->setBaseUrl($baseUrl);
        
        $this->setParam('accessKey', Mage_Api2_Model_Old::getTestAccessKey());
    }

    public function getResourceType()
    {
        //this is set during routing
        $resourceType = $this->getParam('type');

        return $resourceType;
    }

    public function getOperation()
    {
        //we use classical CRUD verbs
        $operationByMethod = array(
            'GET'    => 'retrieve',
            'POST'   => 'create',
            'PUT'    => 'update',
            'DELETE' => 'delete',
        );


        $operation = $operationByMethod[$this->getMethod()];

        return $operation;
    }

    public function getVersion()
    {
        $version = $this->getHeader('Version');

        return $version;
    }

    public function getApiType()
    {
        $route = new Zend_Controller_Router_Route(self::BASE_URL . '*');
        $data = $route->match($this->getRequestUri());
        
        $apiType = $data['api'];

        return $apiType;
    }

    public function getAccessKey()
    {
        //TODO now it's set in request, in real life it should be fetched from HTTP "Authorization" header
        // or as fallback in a query param "key"
        $accessKey = $this->getParam('accessKey');

        /*$string = $this->getHeader('Authorization');
        //$string = 'OAuth 119c61237cd68994c17bcfd5193060ac8ac12239';
        preg_match('/OAuth\s(.+)/', $string, $matches);
        $accessKey = $matches[1];*/

        return $accessKey;
    }

    public function getAcceptType()
    {
        $types = $this->getAcceptTypes();

        return $types[0];
    }

    protected function getAcceptTypes()
    {
        $string = $this->getHeader('Accept');
        list($string) = explode(';', $string);

        $types = explode(',', $string);

        return $types;
    }

    public function getContentType()
    {
        $string = $this->getHeader('Content-Type');
        list($type, $string) = explode(';', $string);
        list(,$charset) = explode('=', $string);

        $object = (object)array('type'=>trim($type), 'charset'=>trim($charset));

        return $object;
    }

    //used?
    private function getResourceParams()
    {
        $specials = array(
            'key',
            'type',
            'model',
            'accessKey'
        );

        $data = $this->getParams();
        foreach ($specials as $attribute) {
            if (isset($data[$attribute])) {
                unset($data[$attribute]);
            }
        }

        return $data;
    }

    /**
     * Fetch data from HTTP Request body
     *
     * @return array
     */
    public function getBodyParams()
    {
        $type = $this->getContentType()->type;

        $interpreter = Mage_Api2_Model_Request_Interpreter::factory($type);
        $params = $interpreter->interpret($this->getRawBody());

        return $params;
    }

    public function getEncoding()
    {
        //TODO in HTTP encoding!=charset, this method is about charset
        //TODO what charset it is request/response?
        //TODO request charset determined by Content-Type HTTP header
        //TODO response charset determined by Accept-Charset HTTP header
        $encoding = 'UTF-8';

        return $encoding;
    }
}
