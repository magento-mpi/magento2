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
 * to license@magento.com so we can send you a copy immediately.
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade Magento to newer
 * versions in the future. If you wish to customize Magento for your
 * needs please refer to http://www.magento.com for more information.
 *
 * @category    Magento
 * @package     Magento
 * @subpackage  integration_tests
 * @copyright   Copyright (c) 2011 Magento Inc. (http://www.magento.com)
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
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
    protected static $_ws;

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
     * Get webservice adapter
     *
     * @param array $options
     * @return Magento_Test_Webservice_Abstract
     */
    public function getWebService($options = null)
    {
        if (null === self::$_ws) {
            $webserviceType = strtolower(TESTS_WEBSERVICE_TYPE);

            $this->_modifyConfig($webserviceType);

            $class = $this->_webServiceMap[$webserviceType];
            self::$_ws = new $class();
            self::$_ws->init($options);
        }
        return self::$_ws;
    }

    /**
     * Call method to webservice
     *
     * @param string $path
     * @param array $params
     * @return string   Return result of request
     */
    public function call($path, $params = array())
    {
        if (null === self::$_ws) {
            $this->getWebService();
        }
        return self::$_ws->call($path, $params);
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
}
