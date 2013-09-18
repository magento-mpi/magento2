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
     * @var \Magento\Core\Helper\Data
     */
    protected $_helper;

    protected function setUp()
    {
        $eventManager = $this->getMock('Magento\Core\Model\Event\Manager', array(), array(), '', false);
        $coreHttp = $this->getMock('Magento\Core\Helper\Http', array(), array(), '', false);
        $contextMock = $this->getMock('Magento\Core\Helper\Context', array(), array(), '', false);
        $configMock = $this->getMock('Magento\Core\Model\Config', array(), array(), '', false);
        $this->_helper = new \Magento\Core\Helper\Data($eventManager, $coreHttp, $contextMock, $configMock);
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
