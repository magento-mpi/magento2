<?php
/**
 * @copyright Copyright (c) 2014 X.commerce, Inc. (http://www.magentocommerce.com)
 */
namespace Magento\Cms\Block;

class PageTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @magentoAppIsolation enabled
     * @magentoDataFixture Magento/Cms/_files/pages.php
     */
    public function testGetPage()
    {
        $page = \Magento\TestFramework\Helper\Bootstrap::getObjectManager()->get('Magento\Cms\Model\Page');
        $page->load('page100', 'identifier');
        $layout = \Magento\TestFramework\Helper\Bootstrap::getObjectManager()->get(
            'Magento\Framework\View\LayoutInterface'
        );
        $pageBlock = $layout->createBlock('Magento\Cms\Block\Page');
        $pageBlock->setData('page', $page);
        $pageBlock->toHtml();
        $this->assertEquals($page, $pageBlock->getPage());
    }
}
