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

class Magento_Test_Webservice extends PHPUnit_Framework_TestCase
{

    protected static $_ws = null;

    /**
     * fixtures registry
     *
     * @var array
     */
    protected static $_fixtures = array();

    private $_webServiceMap = array(
        'soapv1'=>'Magento_Test_Webservice_SoapV1',
        'soapv2'=>'Magento_Test_Webservice_SoapV2',
        'xmlrpc'=>'Magento_Test_Webservice_XmlRpc'
    );


    public function getWebService()
    {
        if (is_null(self::$_ws)) {
            $class = $this->_webServiceMap[strtolower(TESTS_WEBSERVICE_TYPE)];
            self::$_ws = new $class();
            self::$_ws->init();
        }

        return self::$_ws;
    }

    public function call($path, $params = array())
    {
        if(is_null(self::$_ws)) $this->getWebService();

        return self::$_ws->call($path, $params);
    }

    /**
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
        if (!is_null($keyTrimmer)){
            $isTrimmed = true;
        }

        if(is_object($xml)){
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
                    $result[$arrKey] = (string)$node;
                }
            }
        } else {
            $result = (string) $xml;
        }
        return $result;
    }

    /**
     * Set fixture to registry
     *
     * @param string $key
     * @param mixed $fixture
     * @return void
     */
    public static function setFixture($key, $fixture)
    {
        self::$_fixtures[$key] = $fixture;
    }

    /**
     * Get fixture by key
     *
     * @param string $key
     * @return mixed
     */
    public static function getFixture($key)
    {
        if (array_key_exists($key, self::$_fixtures)) {
            return self::$_fixtures[$key];
        }

        return null;
    }
}
