<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */


namespace Magento\Banner\Block\Adminhtml\Banner\Edit\Tab;

/**
 * @magentoAppArea adminhtml
 */
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
