<?php
/**
 * Magento
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://opensource.org/licenses/osl-3.0.php
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@magentocommerce.com so we can send you a copy immediately.
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade Magento to newer
 * versions in the future. If you wish to customize Magento for your
 * needs please refer to http://www.magentocommerce.com for more information.
 *
 * @category    Mage
 * @package     Mage_Api2
 * @copyright   Copyright (c) 2012 Magento Inc. (http://www.magentocommerce.com)
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/**
 * API Request model
 *
 * @category   Mage
 * @package    Mage_Api2
 * @author     Magento Core Team <core@magentocommerce.com>
 */
class Mage_Api2_Model_Request extends Zend_Controller_Request_Http
{
    /**
     * Character set which must be used in request
     */
    const REQUEST_CHARSET = 'utf-8';

    /**
     * Interpreter adapter
     *
     * @var Mage_Api2_Model_Request_Interpreter_Interface
     */
    protected $_interpreter;

    /**
     * Constructor
     *
     * If a $uri is passed, the object will attempt to populate itself using
     * that information.
     * Override parent class to allow object instance get via Mage::getSingleton()
     *
     * @param string|Zend_Uri $uri
     */
    public function __construct($uri = null)
    {
        parent::__construct($uri ? $uri : null);
    }

    /**
     * Get request interpreter
     *
     * @return Mage_Api2_Model_Request_Interpreter_Interface
     */
    protected function _getInterpreter()
    {
        if (null === $this->_interpreter) {
            $this->_interpreter = Mage_Api2_Model_Request_Interpreter::factory($this->getContentType());
        }
        return $this->_interpreter;
    }

    /**
     * Retrieve accept types understandable by requester in a form of array sorted by quality descending
     *
     * @return array
     * @throws Mage_Api2_Exception
     */
    public function getAcceptTypes()
    {
        $qualityToTypes = array();
        $orderedTypes   = array();

        foreach (preg_split('/,\s*/', $this->getHeader('Accept')) as $definition) {
            $typeWithQ = explode(';', $definition);
            $mimeType  = trim(array_shift($typeWithQ));

            // check MIME type validity
            if (!preg_match('~^([0-9a-z*+\-]+)(?:/([0-9a-z*+\-\.]+))?$~i', $mimeType)) {
                continue;
            }
            $quality = '1.0'; // default value for quality

            if ($typeWithQ) {
                $qAndValue = explode('=', $typeWithQ[0]);

                if (2 == count($qAndValue)) {
                    $quality = $qAndValue[1];
                }
            }
            $qualityToTypes[$quality][$mimeType] = true;
        }
        krsort($qualityToTypes);

        foreach ($qualityToTypes as $typeList) {
            $orderedTypes += $typeList;
        }
        return array_keys($orderedTypes);
    }

    /**
     * Get api type from Request
     *
     * @return string
     */
    public function getApiType()
    {
        return $this->getParam('api_type');
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
     * Get Content-Type of request
     *
     * @return string
     * @throws Mage_Api2_Exception
     */
    public function getContentType()
    {
        $headerValue = $this->getHeader('Content-Type');

        if (!$headerValue) {
            throw new Mage_Api2_Exception('Content-Type header is empty', Mage_Api2_Model_Server::HTTP_BAD_REQUEST);
        }
        if (!preg_match('~^([a-z\d/\-+.]+)(?:; *charset=(.+))?$~Ui', $headerValue, $matches)) {
            throw new Mage_Api2_Exception('Invalid Content-Type header', Mage_Api2_Model_Server::HTTP_BAD_REQUEST);
        }
        // request encoding check if it is specified in header
        if (isset($matches[2]) && self::REQUEST_CHARSET != strtolower($matches[2])) {
            throw new Mage_Api2_Exception(
                'UTF-8 is the only supported charset', Mage_Api2_Model_Server::HTTP_BAD_REQUEST
            );
        }
        return $matches[1];
    }

    /**
     * Get resource model class name
     *
     * @return string
     */
    public function getModel()
    {
        return $this->getParam('model');
    }

    /**
     * Retrieve one of CRUD operation dependent on HTTP method
     *
     * @return string
     * @throws Mage_Api2_Exception
     */
    public function getOperation()
    {
        if (!$this->isGet() && !$this->isPost() && !$this->isPut() && !$this->isDelete()) {
            throw new Mage_Api2_Exception('Invalid request method', Mage_Api2_Model_Server::HTTP_BAD_REQUEST);
        }
        // Map HTTP methods to classic CRUD verbs
        $operationByMethod = array(
            'GET'    => Mage_Api2_Model_Resource::OPERATION_RETRIEVE,
            'POST'   => Mage_Api2_Model_Resource::OPERATION_CREATE,
            'PUT'    => Mage_Api2_Model_Resource::OPERATION_UPDATE,
            'DELETE' => Mage_Api2_Model_Resource::OPERATION_DELETE
        );

        return $operationByMethod[$this->getMethod()];
    }

    /**
     * Retrieve resource type
     *
     * @return string
     */
    public function getResourceType()
    {
        return $this->getParam('type');
    }

    /**
     * Get Version header from headers
     *
     * @return string
     */
    public function getVersion()
    {
        return $this->getHeader('Version');
    }
}
