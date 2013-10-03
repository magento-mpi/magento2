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
    const FILE = 'blank.html';

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

    public function testIncludeScript()
    {
        $this->assertEquals('<script type="text/javascript" src="http://localhost/pub/lib/blank.html"></script>' . "\n",
            $this->_helper->includeScript(self::FILE)
        );
        $script = '<script type="text/javascript" src="http://localhost/pub/lib/images/spacer.gif"></script>';
        $this->assertStringMatchesFormat($script, $this->_helper->includeScript('images/spacer.gif'));
    }
}
