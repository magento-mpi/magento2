<?php
/**
 * Helper for API integration tests.
 *
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
class Magento_Test_Helper_Api
{
    /**
     * Call API method via API handler.
     *
     * @param PHPUnit_Framework_TestCase $testCase Active test case
     * @param string $path
     * @param array $params
     * @return mixed
     */
    public static function call(PHPUnit_Framework_TestCase $testCase, $path, $params = array())
    {
        $soapAdapterMock = $testCase->getMock('Mage_Api_Model_Server_Adapter_Soap', array('fault'));
        $soapAdapterMock->expects($testCase->any())->method('fault')->will(
            $testCase->returnCallback(array(__CLASS__, 'soapAdapterFaultCallback'))
        );

        $serverMock = $testCase->getMock('Mage_Api_Model_Server', array('getAdapter'));
        $serverMock->expects($testCase->any())->method('getAdapter')->will($testCase->returnValue($soapAdapterMock));

        $apiSessionMock = $testCase->getMock('Mage_Api_Model_Session', array('isAllowed', 'isLoggedIn'));
        $apiSessionMock->expects($testCase->any())->method('isAllowed')->will($testCase->returnValue(true));
        $apiSessionMock->expects($testCase->any())->method('isLoggedIn')->will($testCase->returnValue(true));

        $handlerMock = $testCase->getMock('Mage_Api_Model_Server_Handler_Soap', array('_getServer', '_getSession'));
        $handlerMock->expects($testCase->any())->method('_getServer')->will($testCase->returnValue($serverMock));
        $handlerMock->expects($testCase->any())->method('_getSession')->will($testCase->returnValue($apiSessionMock));

        array_unshift($params, 'sessionId');
        return call_user_func_array(array($handlerMock, $path), $params);
    }

    /**
     * Throw SoapFault exception. Callback for 'fault' method of API.
     *
     * @param string $exceptionCode
     * @param string $exceptionMessage
     * @throws SoapFault
     */
    public static function soapAdapterFaultCallback($exceptionCode, $exceptionMessage)
    {
        throw new SoapFault($exceptionCode, $exceptionMessage);
    }

    /**
     * Convert Simple XML to array
     *
     * @param SimpleXMLObject $xml
     * @param String $keyTrimmer
     * @return object
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
    public static function simpleXmlToObject($xml, $keyTrimmer = null)
    {
        $result = array();

        $isTrimmed = false;
        if (null !== $keyTrimmer) {
            $isTrimmed = true;
        }

        if (is_object($xml)) {
            foreach (get_object_vars($xml->children()) as $key => $node) {
                $arrKey = $key;
                if ($isTrimmed) {
                    $arrKey = str_replace($keyTrimmer, '', $key); //, &$isTrimmed);
                }
                if (is_numeric($arrKey)) {
                    $arrKey = 'Obj' . $arrKey;
                }
                if (is_object($node)) {
                    $result[$arrKey] = self::simpleXmlToObject($node, $keyTrimmer);
                } elseif (is_array($node)) {
                    $result[$arrKey] = array();
                    foreach ($node as $nodeValue) {
                        $result[$arrKey][] = self::simpleXmlToObject(
                            $nodeValue,
                            $keyTrimmer
                        );
                    }
                } else {
                    $result[$arrKey] = (string)$node;
                }
            }
        } else {
            $result = (string)$xml;
        }
        return (object)$result;
    }
}
