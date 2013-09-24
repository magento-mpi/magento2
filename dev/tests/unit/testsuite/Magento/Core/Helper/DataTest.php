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
        $coreConfig = $this->getMock('Magento_Core_Model_Config', array(), array(), '', false);
        $coreStoreConfig = $this->getMock('Magento_Core_Model_Store_Config', array(), array(), '', false);
        $this->_helper = new Magento_Core_Helper_Data(
            $eventManager,
            $coreHttp,
            $contextMock,
            $coreConfig,
            $coreStoreConfig,
            true
        );
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
