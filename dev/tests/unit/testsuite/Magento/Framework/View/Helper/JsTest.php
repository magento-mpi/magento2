<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Framework\View\Helper;

class JsTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @covers \Magento\Framework\View\Helper\Js::getScript
     */
    public function testGetScript()
    {
        $helper = new \Magento\Framework\View\Helper\Js();
        $this->assertEquals(
            "<script type=\"text/javascript\">//<![CDATA[\ntest\n//]]></script>",
            $helper->getScript('test')
        );
    }
}
