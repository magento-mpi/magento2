<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Mage_Core
 * @subpackage  integration_tests
 * @copyright   {copyright}
 * @license     {license_link}
 */

class Mage_Core_Helper_JsTest extends PHPUnit_Framework_TestCase
{
    const FILE = 'blank.html';

    /**
     * @var Mage_Core_Helper_Js
     */
    protected $_helper;

    public function setUp()
    {
        $this->_helper = new Mage_Core_Helper_Js();
    }

    protected function tearDown()
    {
        $this->_helper = null;
    }

    public function testGetTranslateJson()
    {
        $this->assertNotNull(json_decode($this->_helper->getTranslateJson()));
    }

    public function testGetTranslatorScript()
    {
        $this->assertEquals(
            '<script type="text/javascript">//<![CDATA['
                . "\nvar Translator = new Translate({$this->_helper->getTranslateJson()});\n"
                . '//]]></script>',
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
        $this->assertEquals('<script type="text/javascript" src="http://localhost/pub/js/blank.html"></script>' . "\n",
            $this->_helper->includeScript(self::FILE)
        );
        $script = '<script type="text/javascript" '
            . 'src="http://localhost/pub/media/skin/frontend/%s/%s/%s/%s/images/spacer.gif"></script>';
        $this->assertStringMatchesFormat($script, $this->_helper->includeScript('images/spacer.gif'));
    }
}
