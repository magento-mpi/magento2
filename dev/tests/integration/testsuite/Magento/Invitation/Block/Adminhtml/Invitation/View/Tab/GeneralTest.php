<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Invitation\Block\Adminhtml\Invitation\View\Tab;

/**
 * Invitation create form
 *
 * @magentoAppArea adminhtml
 */
class GeneralTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @magentoAppIsolation enabled
     */
    public function testPrepareFormForCustomerGroup()
    {
        /** @var $objectManager \Magento\TestFramework\ObjectManager */
        $objectManager = \Magento\TestFramework\Helper\Bootstrap::getObjectManager();
        $objectManager->get(
            'Magento\View\DesignInterface'
        )->setArea(
            \Magento\Backend\App\Area\FrontNameResolver::AREA_CODE
        )->setDefaultDesignTheme();

        $block = $objectManager->create('Magento\Invitation\Block\Adminhtml\Invitation\View\Tab\General');

        $this->assertContains("General", $block->getCustomerGroupCode(1));
        $this->assertContains("Wholesale", $block->getCustomerGroupCode(2));
        $this->assertContains("Retailer", $block->getCustomerGroupCode(3));
    }
}
