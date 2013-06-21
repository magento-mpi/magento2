<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Mage_Catalog
 * @subpackage  unit_tests
 * @copyright   {copyright}
 * @license     {license_link}
 */

class Mage_Catalog_Helper_UrlTest extends PHPUnit_Framework_TestCase
{
    /** @var Mage_Core_Model_Config */
    protected $_configMock;

    /** @var  Mage_Catalog_Helper_Product_Url */
    protected $_urlHelper;

    protected function setUp()
    {
        $contextMock = $this->getMockBuilder('Mage_Core_Helper_Context')->disableOriginalConstructor()->getMock();
        $this->_configMock = $this->getMockBuilder('Mage_Core_Model_Config')->disableOriginalConstructor()->getMock();

        $this->_urlHelper = new Mage_Catalog_Helper_Product_Url($contextMock, $this->_configMock);
    }

    /**
     * @param string $testString
     * @param string $result
     *
     * @dataProvider validetStringFormat
     */
    public function testFormat($testString, $result)
    {
        $this->assertEquals($result, $this->_urlHelper->format($testString));
    }


    public static function validetStringFormat()
    {
        return array(
            array('test', 'test'),
            array('привет мир', 'privet mir'),
            array(
                'Weiß, Goldmann, Göbel, Weiss, Göthe, Goethe und Götz',
                'Weiss, Goldmann, Gobel, Weiss, Gothe, Goethe und Gotz'
            ),
            array(
                '❤ ☀ ☆ ☂ ☻ ♞ ☯ ☭ ☢ € → ☎ ❄ ♫ ✂ ▷ ✇ ♎ ⇧ ☮',
                '❤ ☀ ☆ ☂ ☻ ♞ ☯ ☭ ☢ € → ☎ ❄ ♫ ✂ ▷ ✇ ♎ ⇧ ☮'
            ),
        );
    }
}