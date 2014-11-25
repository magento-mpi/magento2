<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Cms\Block;

/**
 * Class PageTest
 */
class PageTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var \Magento\Cms\Block\Page
     */
    protected $block;

    /**
     * @var \Magento\Cms\Model\Page
     */
    protected $page;

    protected function setUp()
    {
        $objectManager = new \Magento\TestFramework\Helper\ObjectManager($this);
        $this->block = $objectManager->getObject('Magento\Cms\Block\Page');
        $this->page = $objectManager->getObject('Magento\Cms\Model\Page');
        $this->page->setPageId(1);
    }

    protected function tearDown()
    {
        $this->block = null;
    }

    public function testGetIdentities()
    {
        $id = 1;
        $this->block->setPage($this->page);
        $this->assertEquals(
            [\Magento\Cms\Model\Page::CACHE_TAG . '_' . $id],
            $this->block->getIdentities()
        );
    }
}
