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

/**
 * @group module:Mage_Core
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
        $this->_helper = new Mage_Core_Helper_Js;
    }

    public function testGetTranslateJson()
    {
        $this->assertNotNull(json_decode($this->_helper->getTranslateJson()));
    }

    public function testGetTranslatorScript()
    {
        $this->assertEquals(
            '<script type="text/javascript">var Translator = new Translate('
            . $this->_helper->getTranslateJson()
            . ');</script>',
            $this->_helper->getTranslatorScript()
        );
    }

    public function testGetScript()
    {
        $this->assertEquals('<script type="text/javascript">script</script>', $this->_helper->getScript('script'));
    }

    public function testIncludeScript()
    {
        $this->assertEquals('<script type="text/javascript" src="http://localhost/js/blank.html"></script>' . "\n",
            $this->_helper->includeScript(self::FILE)
        );
    }

    public function testIncludeSkinScript()
    {
        $this->assertStringMatchesFormat(
            '<script type="text/javascript" src="http://localhost/skin/frontend/%s/%s/images/spacer.gif"></script>',
            $this->_helper->includeSkinScript('images/spacer.gif')
        );
    }
}
