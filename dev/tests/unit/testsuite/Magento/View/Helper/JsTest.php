<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\View\Helper;

class JsTest extends \PHPUnit_Framework_TestCase
{

    /**
     * @covers \Magento\View\Helper\Js::getScript
     */
    public function testGetScript()
    {
        $helper = new \Magento\View\Helper\Js();
        $this->assertEquals(
            "<script type=\"text/javascript\">//<![CDATA[\ntest\n//]]></script>",
            $helper->getScript('test')
        );
    }
}
