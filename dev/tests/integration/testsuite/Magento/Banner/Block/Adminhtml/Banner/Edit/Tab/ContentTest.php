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
namespace Magento\Banner\Block\Adminhtml\Banner\Edit\Tab;

class ContentTest extends \PHPUnit_Framework_TestCase
{
    public function testConstruct()
    {
        $this->assertInstanceOf(
            'Magento\Banner\Block\Adminhtml\Banner\Edit\Tab\Content',
            \Mage::app()->getLayout()->createBlock('Magento\Banner\Block\Adminhtml\Banner\Edit\Tab\Content')
        );
    }
}
