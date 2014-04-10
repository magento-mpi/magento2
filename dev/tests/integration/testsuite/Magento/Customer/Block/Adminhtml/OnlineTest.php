<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Adminhtml
 * @subpackage  integration_tests
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Customer\Block\Adminhtml;

/**
 * @magentoAppArea adminhtml
 */
class OnlineTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @magentoAppIsolation enabled
     */
    public function testGetFilterFormHtml()
    {
        /** @var $layout \Magento\View\LayoutInterface */
        $layout = \Magento\TestFramework\Helper\Bootstrap::getObjectManager()->create(
            'Magento\View\Layout',
            array('area' => \Magento\Backend\App\Area\FrontNameResolver::AREA_CODE)
        );
        /** @var $block \Magento\Customer\Block\Adminhtml\Online */
        $block = $layout->createBlock('Magento\Customer\Block\Adminhtml\Online', 'block');
        $this->assertNotEmpty($block->getFilterFormHtml());
    }
}
