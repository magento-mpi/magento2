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

    /**
     * Interpreter adapter
     *
     * @var Mage_Api2_Model_Request_Interpreter_Interface
     */
    protected $_interpreter;

    public function __construct()
    {
        $replace = array(':api' => $this->getApiType('rest'));
        $baseUrl = strtr(self::BASE_URL, $replace);
        $this->setBaseUrl($baseUrl);

        $this->setParam('accessKey', Mage_Api2_Model_Old::getTestAccessKey());
    }

    /**
     * @return Mage_Api2_Model_Request_Interpreter_Interface
     */
    protected function _getInterpreter()
    {
        if (null === $this->_interpreter) {
            $this->_interpreter = Mage_Api2_Model_Request_Interpreter::factory(
                $this->getContentType()->type
            );
        }
        return $this->_interpreter;
    }

    /**
     * Fetch data from HTTP Request body
     *
     * @return array
     */
    public function getBodyParams()
    {
        return $this->_getInterpreter()->interpret($this->getRawBody());
    }

    /**
     * Get Content-Type of Request body parsed into object
     *
     * @return stdClass
     */
    public function getContentType()
    {
        $string = $this->getHeader('Content-Type');
        list($type, $string) = explode(';', $string);
        list(, $charset) = explode('=', $string);

        return ((object) array('type' => trim($type), 'charset' => trim($charset)));
    }

    public function getAcceptTypes()
    {
        $string = $this->getHeader('Accept');
        $definitions = preg_split('/,\s*/', $string);

        //Assume that same quality values are forbidden and no two MIME types with out "q" at all
        $groups = array();
        foreach ($definitions as $definition) {
            $array = explode(';', $definition);
            $type = trim(array_shift($array));

            $params = array();
            foreach ($array as $param) {
                $pair = explode('=', $param, 2);
                if (count($pair)!=2) {
                    continue;
                }

                $params[trim($pair[0])] = trim($pair[1]);
            }

            //TODO params except "q" not used and not saved anywhere
            $quality = isset($params['q'])  ?$params['q']   :'1.0';     //No "q" means "q=1"

            unset($params['q']);
            list(, $subtype) = explode('/', $type);
            if ($type == '*/*') {
                $group = 1;
            } elseif ($subtype=='*') {
                $group = 2;
            } elseif (count($params)>0) {
                $group = 4;
            } else {
                $group = 3;
            }

            $groups[(string)$quality][$group][] = $type;
        }

        krsort($groups);
        foreach ($groups as &$group) {
            krsort($group);
        }

        $types = array();
        $iterator = new RecursiveIteratorIterator(new RecursiveArrayIterator($groups));
        foreach ($iterator as $item) {
            $types[] = $item;
        }

        return $types;
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
            'GET'    => Mage_Api2_Model_Resource::OPERATION_RETRIEVE,
            'POST'   => Mage_Api2_Model_Resource::OPERATION_CREATE,
            'PUT'    => Mage_Api2_Model_Resource::OPERATION_UPDATE,
            'DELETE' => Mage_Api2_Model_Resource::OPERATION_DELETE,
        );

        $operation = $operationByMethod[$this->getMethod()];

        return $operation;
    }

    /**
     * Get Version header from headers
     *
     * @return false|string
     */
    public function getVersion()
    {
        return $this->getHeader('Version');
    }

    /**
     * Get api type from Request
     *
     * @return string
     */
    public function getApiType()
    {
        $route = new Zend_Controller_Router_Route(self::BASE_URL . '*');
        $data = $route->match($this->getRequestUri());

        return $data['api'];
    }

    public function getAccessKey()
    {
        //TODO now it's set in request __construct(), in real life it should be fetched from HTTP "Authorization" header
        // or as fallback in a query param "key"
        $accessKey = $this->getParam('accessKey');

        /*$string = $this->getHeader('Authorization');
        //example:: $string = 'OAuth 119c61237cd68994c17bcfd5193060ac8ac12239';
        preg_match('/OAuth\s(.+)/', $string, $matches);
        $accessKey = $matches[1];*/

        return $accessKey;
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

    public function getAcceptCharset()
    {
        //TODO in HTTP encoding!=charset, this method is about charset
        //TODO what charset it is request/response?
        // * request charset determined by Content-Type HTTP header
        // * response charset determined by Accept-Charset HTTP header
        $encoding = 'utf-8';

        return $encoding;
    }
}
