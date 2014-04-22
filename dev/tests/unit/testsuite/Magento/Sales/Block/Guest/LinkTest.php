<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Sales
 * @subpackage  unit_tests
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Sales\Block\Guest;

/**
 * Test class for \Magento\Sales\Block\Guest\Link
 */
class LinkTest extends \PHPUnit_Framework_TestCase
{
    public function testToHtml()
    {
        $objectManagerHelper = new \Magento\TestFramework\Helper\ObjectManager($this);

        $context = $objectManagerHelper->getObject('Magento\Framework\View\Element\Template\Context');
        $httpContext = $this->getMockBuilder('\Magento\Framework\App\Http\Context')
            ->disableOriginalConstructor()
            ->setMethods(array('getValue'))
            ->getMock();
        $httpContext->expects($this->once())
            ->method('getValue')
            ->will($this->returnValue(true));

        /** @var \Magento\Sales\Block\Guest\Link $link */
        $link = $objectManagerHelper->getObject(
            'Magento\Sales\Block\Guest\Link',
            array(
                'context' => $context,
                'httpContext' => $httpContext,
            )
        );

        $this->assertEquals('', $link->toHtml());
    }
}
