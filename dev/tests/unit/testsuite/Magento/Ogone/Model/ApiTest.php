<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

class Magento_Ogone_Model_ApiTest extends PHPUnit_Framework_TestCase
{
    protected function setUp()
    {
        $this->markTestSkipped('Api tests were skipped');
    }

    /**
     * Test protected method, which converts Magento internal charset (UTF-8) to the one, understandable
     * by Ogone (ISO-8859-1), and then encodes html entities
     */
    public function testTranslate()
    {
        /* Compose the string, which, when converted to ISO-8859-1, still looks like a valid UTF-8 string.
           So that the latter result of htmlentities() is different, depending on the encoding used for it. */
        $sourceString = 'Ë£';

        // Test protected method via reflection
        $storeManager = $this->getMock('Magento_Core_Model_StoreManagerInterface', array(), array(), '', false);
        $locale = $this->getMock('Magento_Core_Model_LocaleInterface', array(), array(), '', false);
        $urlBuilder = $this->getMock('Magento_Core_Model_UrlInterface', array(), array(), '', false);
        $eventManager = $this->getMock('Magento_Core_Model_Event_Manager', array(), array(), '', false);
        $coreString = $this->getMock('Magento\Core\Helper\String', array(), array(), '', false);
        $config = $this->getMock('Magento\Ogone\Model\Config', array(), array(), '', false);
        $paymentDataMock = $this->getMock('Magento\Payment\Helper\Data', array(), array(), '', false);
        $coreStoreConfig = $this->getMock('Magento\Core\Model\Store\Config', array(), array(), '', false);
        $object = new Magento_Ogone_Model_Api(
            $storeManager, $locale, $urlBuilder, $eventManager,
            $coreString, $coreStoreConfig, $config, $paymentDataMock
        );

        $method = new ReflectionMethod('Magento\Ogone\Model\Api', '_translate');
        $method->setAccessible(true);

        $result = $method->invoke($object, $sourceString);
        $this->assertEquals('&Euml;&pound;', $result);
    }
}
