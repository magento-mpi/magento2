<?php
/**
 * {license_notice}
 *
 * @subpackage  unit_tests
 * @copyright   {copyright}
 * @license     {license_link}
 */
class Magento_Core_Helper_DataTest extends PHPUnit_Framework_TestCase
{
    /**
     * @var Magento_Core_Helper_Data
     */
    protected $_helper;

    protected function setUp()
    {
        $eventManager = $this->getMock('Magento_Core_Model_Event_Manager', array(), array(), '', false);
        $coreHttp = $this->getMock('Magento_Core_Helper_Http', array(), array(), '', false);
        $contextMock = $this->getMock('Magento_Core_Helper_Context', array(), array(), '', false);
        $configMock = $this->getMock('Magento_Core_Model_Config', array(), array(), '', false);
        $storeManager = $this->getMock('Magento_Core_Model_StoreManager', array(), array(), '', false);
        $locale = $this->getMock('Magento_Core_Model_Locale_Proxy', array(), array(), '', false);
        $date = $this->getMock('Magento_Core_Model_Date_Proxy', array(), array(), '', false);
        $appState = $this->getMock('Magento_Core_Model_App_State', array(), array(), '', false);
        $configResource = $this->getMock('Magento_Core_Model_Config_Resource', array(), array(), '', false);
        $this->_helper = new Magento_Core_Helper_Data($eventManager, $coreHttp, $contextMock, $configMock,
            $storeManager, $locale, $date, $appState, $configResource);
    }

    /**
     * @param string $string
     * @param bool $german
     * @param string $expected
     *
     * @dataProvider removeAccentsDataProvider
     */
    public function testRemoveAccents($string, $german, $expected)
    {
        $this->assertEquals($expected, $this->_helper->removeAccents($string, $german));
    }

    /**
     * @return array
     */
    public function removeAccentsDataProvider()
    {
        return array(
            'general conversion' => array(
                'ABCDEFGHIJKLMNOPQRSTUVWXYZ',
                false,
                'ABCDEFGHIJKLMNOPQRSTUVWXYZ',
            ),
            'conversion with german specifics' => array(
                'äöüÄÖÜß',
                true,
                'aeoeueAeOeUess'
            ),
        );
    }
}
