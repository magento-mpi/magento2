<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * @magentoAppArea adminhtml
 */
namespace Magento\Adminhtml\Block\Promo\Quote\Edit\Tab;

class LabelsTest extends \PHPUnit_Framework_TestCase
{
    public function testConstruct()
    {
        $this->assertInstanceOf(
            'Magento\Adminhtml\Block\Promo\Quote\Edit\Tab\Labels',
            \Mage::app()->getLayout()->createBlock('Magento\Adminhtml\Block\Promo\Quote\Edit\Tab\Labels')
        );
    }
}
