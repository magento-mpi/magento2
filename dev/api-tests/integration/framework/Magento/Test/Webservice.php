<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento
 * @subpackage  integration_tests
 * @copyright   {copyright}
 * @license     {license_link}
 */

class Magento_Test_Webservice extends Magento_TestCase
{
    /**#@+
     * Webservice type
     */
    const TYPE_SOAPV1     = 'soapv1';
    const TYPE_SOAPV2     = 'soapv2';
    const TYPE_SOAPV2_WSI = 'soapv2_wsi';
    const TYPE_XMLRPC     = 'xmlrpc';
    /**#@-*/

    const DEFAULT_EXCEPTION = 'DEFAULT_EXCEPTION';
    /**
     * Webservice adapter
     *
     * @var Magento_Test_Webservice_Abstract
     */
    protected static $_adapterRegistry;

    /**
     * Default webservice adapter
     *
     * @var string
     */
    protected static $_defaultAdapter = 'default';

    /**
     * Clients class name list
     *
     * @var array
     */
    protected $_webServiceMap = array(
        self::TYPE_SOAPV1     => 'Magento_Test_Webservice_SoapV1',
        self::TYPE_SOAPV2     => 'Magento_Test_Webservice_SoapV2',
        self::TYPE_SOAPV2_WSI => 'Magento_Test_Webservice_SoapV2_Wsi',
        self::TYPE_XMLRPC     => 'Magento_Test_Webservice_XmlRpc'
    );

    /**
     * Default helper for current test suite
     *
     * @var string
     */
    protected $_defaultHelper = 'Helper_Catalog_Product_Simple';

    /** @var array */
    protected $_helpers = array();

    /**
     * Get current test suite helper if class name not specified.
     *
     * @param string|null $helperClass
     * @return mixed
     */
    protected function _getHelper($helperClass = null)
    {
        if (is_null($helperClass)) {
            $helperClass = $this->_defaultHelper;
        }

        if (!isset($this->_helpers[$helperClass])) {
            $this->_helpers[$helperClass] = new $helperClass;
        }

        return $this->_helpers[$helperClass];
    }

    /**
     * Modify config settings on systen been tested for specified webservice type
     *
     * @param string $webserviceType Webservice type. One of self::TYPE_... constant
     */
    protected function _modifyConfig($webserviceType)
    {
        if (self::TYPE_SOAPV2 == $webserviceType) {
            if (Mage::getStoreConfig('api/config/compliance_wsi')) {
                $this->_updateAppConfig('api/config/compliance_wsi', 0, true, true);
            }
        } elseif (self::TYPE_SOAPV2_WSI == $webserviceType) {
            if (!Mage::getStoreConfig('api/config/compliance_wsi')) {
                $this->_updateAppConfig('api/config/compliance_wsi', 1, true, true);
            }
        }
    }

    /**
     * Get adapter instance
     *
     * @param string $code
     * @return Magento_Test_Webservice_Abstract
     */
    protected function getInstance($code)
    {
        $instance = null;
        if (isset(self::$_adapterRegistry[$code])){
            $instance = self::$_adapterRegistry[$code];
        }

        return $instance;
    }

    /**
     * Set adapter instance
     *
     * @param string $code
     * @param Magento_Test_Webservice_Abstract $instance
     */
    protected function setInstance($code, Magento_Test_Webservice_Abstract $instance)
    {
        self::$_adapterRegistry[$code] = $instance;
    }

    /**
     * Get webservice adapter
     *
     * @param string $code
     * @param array $options
     * @return Magento_Test_Webservice_Abstract
     */
    public function getWebService($code = null, $options = null)
    {
        if (!$code) {
            $code = self::$_defaultAdapter;
        }
        if (null === $this->getInstance($code)) {
            $webserviceType = strtolower(TESTS_WEBSERVICE_TYPE);
            $this->_modifyConfig($webserviceType);
            $class = $this->_webServiceMap[$webserviceType];

            $this->setInstance($code, new $class());
            $this->getInstance($code)->init($options);
        }

        return $this->getInstance($code);
    }

    /**
     * Call method to webservice
     *
     * @param string $path
     * @param array $params
     * @param string $code
     * @return string   Return result of request
     */
    public function call($path, $params = array(), $code = 'default')
    {
        if (null === $this->getInstance($code)) {
            $this->getWebService($code);
        }
        return $this->getInstance($code)->call($path, $params);
    }

    /**
     * Convert Simple XML to array
     *
     * @param SimpleXMLObject $xml
     * @param String $keyTrimmer
     * @return array
     *
     * In XML notation we can't have nodes with digital names in other words fallowing XML will be not valid:
     * &lt;24&gt;
     *      Default category
     * &lt;/24&gt;
     *
     * But this one will not cause any problems:
     * &lt;qwe_24&gt;
     *      Default category
     * &lt;/qwe_24&gt;
     *
     * So when we want to obtain an array with key 24 we will pass the correct XML from above and $keyTrimmer = 'qwe_';
     * As a result we will obtain an array with digital key node.
     *
     * In the other case just don't pass the $keyTrimmer.
     */
    public static function simpleXmlToArray($xml, $keyTrimmer = null)
    {
        $result = array();

        $isTrimmed = false;
        if (null !== $keyTrimmer){
            $isTrimmed = true;
        }

        if (is_object($xml)){
            foreach (get_object_vars($xml->children()) as $key => $node)
            {
                $arrKey = $key;
                if ($isTrimmed){
                    $arrKey = str_replace($keyTrimmer, '', $key);//, &$isTrimmed);
                }
                if (is_numeric($arrKey)){
                    $arrKey = 'Obj' . $arrKey;
                }
                if (is_object($node)){
                    $result[$arrKey] = Magento_Test_Webservice::simpleXmlToArray($node, $keyTrimmer);
                } elseif(is_array($node)){
                    $result[$arrKey] = array();
                    foreach($node as $node_key => $node_value){
                        $result[$arrKey][] = Magento_Test_Webservice::simpleXmlToArray($node_value, $keyTrimmer);
                    }
                } else {
                    $result[$arrKey] = (string) $node;
                }
            }
        } else {
            $result = (string) $xml;
        }
        return $result;
    }

    /**
     * @param  mixed   $exceptionName
     * @param  string  $exceptionMessage
     * @param  integer $exceptionCode
     */
    public function setExpectedException($exceptionName, $exceptionMessage = '', $exceptionCode = NULL)
    {
        if ($exceptionName == self::DEFAULT_EXCEPTION) {

            switch (TESTS_WEBSERVICE_TYPE) {
                case self::TYPE_SOAPV1:
                case self::TYPE_SOAPV2:
                case self::TYPE_SOAPV2_WSI:
                    $exceptionName = 'SoapFault';
                    break;
                case self::TYPE_XMLRPC:
                    $exceptionName = 'Zend_XmlRpc_Client_FaultException';
                    break;
                default:
                    throw new Magento_Test_Exception('Webservice type is undefined');
                    break;
            }
        }
        parent::setExpectedException($exceptionName, $exceptionMessage, $exceptionCode);
    }

    /**
    * Assert that two products are equal.
    *
    * @param Mage_Catalog_Model_Product $expected
    * @param Mage_Catalog_Model_Product $actual
    */
    public function assertProductEquals(Mage_Catalog_Model_Product $expected, Mage_Catalog_Model_Product $actual) {
        foreach ($expected->getData() as $attribute => $value) {
            $this->assertEquals($value, $actual->getData($attribute),
                sprintf('Attribute "%s" value does not equal to expected "%s".', $attribute, $value));
        }
    }

    /**
     * Check if all error messages are expected ones
     *
     * @param array $expectedMessages
     * @param array $receivedMessages
     */
    public function assertMessagesEqual($expectedMessages, $receivedMessages)
    {
        foreach ($receivedMessages as $message) {
            $this->assertContains($message, $expectedMessages, "Unexpected message: '$message'");
        }
        $expectedErrorsCount = count($expectedMessages);
        $this->assertCount($expectedErrorsCount, $receivedMessages, 'Invalid messages quantity received');
    }
}
