<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Adminhtml\Block\Promo\Quote\Edit\Tab;

/**
 * @magentoAppArea adminhtml
 */
class LabelsTest extends \PHPUnit_Framework_TestCase
{
    public function testConstruct()
    {
        $this->assertInstanceOf(
            'Magento\Adminhtml\Block\Promo\Quote\Edit\Tab\Labels',
            \Magento\TestFramework\Helper\Bootstrap::getObjectManager()->get('Magento\Core\Model\Layout')
                ->createBlock('Magento\Adminhtml\Block\Promo\Quote\Edit\Tab\Labels')
        );
    }
}
