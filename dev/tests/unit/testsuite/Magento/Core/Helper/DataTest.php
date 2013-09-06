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
        $contextMock = $this->getMock('Magento_Core_Helper_Context', array(), array(), '', false);
        $configMock = $this->getMock('Magento_Core_Model_Config_Modules', array(), array(), '', false);
        $coreStoreConfig = $this->getMock('Magento_Core_Model_Store_Config', array(), array(), '', false);
        $coreConfig = $this->getMock('Magento_Core_Model_Config', array(), array(), '', false);
        $this->_helper = new Magento_Core_Helper_Data($contextMock, $configMock, $coreStoreConfig, $coreConfig);
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
