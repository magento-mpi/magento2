<?php
/**
 * {license_notice}
 *
 * @subpackage  unit_tests
 * @copyright   {copyright}
 * @license     {license_link}
 */
class Mage_Core_Helper_DataTest extends PHPUnit_Framework_TestCase
{
    /**
     * @var Mage_Core_Helper_Data
     */
    protected $_helper;

    protected function setUp()
    {
        $contextMock = $this->getMock('Mage_Core_Helper_Context', array(), array(), '', false);
        $configMock = $this->getMock('Mage_Core_Model_Config', array(), array(), '', false);
        $this->_helper = new Mage_Core_Helper_Data($contextMock, $configMock);
    }

    /**
     * @param string $string
     * @param bool $gearman
     * @param string $expected
     *
     * @dataProvider removeAccentsDataProvider
     */
    public function testRemoveAccents($string, $gearman, $expected)
    {
        $this->assertEquals($expected, $this->_helper->removeAccents($string, $gearman));
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