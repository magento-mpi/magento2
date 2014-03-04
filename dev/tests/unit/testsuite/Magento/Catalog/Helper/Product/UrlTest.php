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

namespace Magento\Catalog\Helper\Product;

class UrlTest extends \PHPUnit_Framework_TestCase
{
    /** @var \Magento\App\ConfigInterface */
    protected $_configMock;

    /** @var  \Magento\Catalog\Helper\Product\Url */
    protected $_urlHelper;

    protected function setUp()
    {
        $contextMock = $this->getMockBuilder('Magento\App\Helper\Context')->disableOriginalConstructor()->getMock();
        $this->_configMock = $this->getMockBuilder('Magento\App\ConfigInterface')
            ->disableOriginalConstructor()->getMock();
        $storeManager = $this->getMock('Magento\Core\Model\StoreManager', array(), array(), '', false);
        $this->_urlHelper = new \Magento\Catalog\Helper\Product\Url(
            $contextMock,
            $storeManager,
            $this->_configMock
        );
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
