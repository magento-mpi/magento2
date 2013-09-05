<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Catalog
 * @subpackage  unit_tests
 * @copyright   {copyright}
 * @license     {license_link}
 */

class Magento_Catalog_Helper_UrlTest extends PHPUnit_Framework_TestCase
{
    /** @var Magento_Core_Model_Config */
    protected $_configMock;

    /** @var  Magento_Catalog_Helper_Product_Url */
    protected $_urlHelper;

    protected function setUp()
    {
        $contextMock = $this->getMockBuilder('Magento_Core_Helper_Context')->disableOriginalConstructor()->getMock();
        $this->_configMock = $this->getMockBuilder('Magento_Core_Model_Config')
            ->disableOriginalConstructor()->getMock();

        $this->_urlHelper = new Magento_Catalog_Helper_Product_Url($contextMock, $this->_configMock);
    }

    /**
     * @param string $testString
     * @param string $result
     * @param string $resultIconv
     * @param bool $isIconv
     * @dataProvider validateStringFormat
     */
    public function testFormat($testString, $result, $resultIconv, $isIconv)
    {
        if ($isIconv) {
            $this->assertEquals($resultIconv, $this->_urlHelper->format($testString));
        } else {
            $this->assertEquals($result, $this->_urlHelper->format($testString));
        }
    }

    /**
     * @return array
     */
    public static function validateStringFormat()
    {
        $isIconv = '"libiconv"' == ICONV_IMPL;
        return array(
            array('test', 'test', 'test', $isIconv),
            array('привет мир', 'privet mir', 'privet mir', $isIconv),
            array(
                'Weiß, Goldmann, Göbel, Weiss, Göthe, Goethe und Götz',
                'Weiss, Goldmann, Gobel, Weiss, Gothe, Goethe und Gotz',
                'Weiss, Goldmann, Gobel, Weiss, Gothe, Goethe und Gotz',
                $isIconv
            ),
            array(
                '❤ ☀ ☆ ☂ ☻ ♞ ☯ ☭ ☢ € → ☎ ❄ ♫ ✂ ▷ ✇ ♎ ⇧ ☮',
                '❤ ☀ ☆ ☂ ☻ ♞ ☯ ☭ ☢ € → ☎ ❄ ♫ ✂ ▷ ✇ ♎ ⇧ ☮',
                '         EUR ->         ',
                $isIconv
            ),
        );
    }
}
