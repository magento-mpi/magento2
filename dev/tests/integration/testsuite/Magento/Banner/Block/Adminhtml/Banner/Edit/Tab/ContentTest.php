<?php
/**
 * @copyright Copyright (c) 2014 X.commerce, Inc. (http://www.magentocommerce.com)
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
            \Magento\TestFramework\Helper\Bootstrap::getObjectManager()->get(
                'Magento\Framework\View\LayoutInterface'
            )->createBlock(
                'Magento\Banner\Block\Adminhtml\Banner\Edit\Tab\Content'
            )
        );
    }
}
