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

    public function testGetTranslatorScript()
    {
        $this->assertEquals(
            "<script type=\"text/javascript\">//<![CDATA[\n" .
            '(function($) {$.mage.translate.add(' .
            $this->_helper->encode($this->_helper->getTranslateData()) .
            ')})(jQuery);' .
            "\n//]]></script>",
            $this->_helper->getTranslatorScript()
        );
    }
}
