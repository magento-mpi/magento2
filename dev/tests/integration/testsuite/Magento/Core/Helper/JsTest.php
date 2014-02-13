<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Core
 * @subpackage  integration_tests
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Core\Helper;

class JsTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var \Magento\Core\Helper\Js
     */
    protected $_helper;

    protected function setUp()
    {
        $this->_helper = \Magento\TestFramework\Helper\Bootstrap::getObjectManager()->get('Magento\Core\Helper\Js');
    }

    public function testGetTranslateJson()
    {
        $this->assertNotNull(json_decode($this->_helper->getTranslateJson()));
    }

    public function testGetTranslatorScript()
    {
        $this->assertEquals(
            "<script type=\"text/javascript\">//<![CDATA[\n"
                . '(function($) {$.mage.translate.add(' . $this->_helper->getTranslateJson() . ')})(jQuery);'
                . "\n//]]></script>",
            $this->_helper->getTranslatorScript()
        );
    }

    public function testGetScript()
    {
        $this->assertEquals("<script type=\"text/javascript\">//<![CDATA[\ntest\n//]]></script>",
            $this->_helper->getScript('test')
        );
    }

    /**
     * @dataProvider includeScriptDataProvider
     * @magentoAppArea frontend
     */
    public function testIncludeScript($file, $expected)
    {
        $this->assertEquals($expected, $this->_helper->includeScript($file));
    }

    /**
     * @return array
     */
    public static function includeScriptDataProvider()
    {
        return array(
            'base file' => array(
                'blank.html',
                '<script type="text/javascript"'
                    . ' src="http://localhost/pub/static/frontend/magento_plushe/en_US/blank.html"></script>'
                    . "\n",
            ),
            'subdir file' => array(
                'images/spacer.gif',
                '<script type="text/javascript"'
                    . ' src="http://localhost/pub/static/frontend/magento_plushe/en_US/images/spacer.gif">'
                    . "</script>\n",
            ),
        );
    }
}
