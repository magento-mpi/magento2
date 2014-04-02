<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Catalog\Block;

class NavigationTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var \Magento\Catalog\Block\Navigation
     */
    protected $block;

    protected function setUp()
    {
        $objectManager = new \Magento\TestFramework\Helper\ObjectManager($this);
        $categoryFactory = $this->getMock(
            'Magento\Catalog\Model\CategoryFactory',
            array('create'),
            array(),
            '',
            false
        );
        $this->block = $objectManager->getObject(
            'Magento\Catalog\Block\Navigation',
            array('categoryFactory' => $categoryFactory)
        );
    }

    protected function tearDown()
    {
        $this->block = null;
    }

    public function testGetIdentities()
    {
        $this->assertEquals(
            array(\Magento\Catalog\Model\Category::CACHE_TAG, \Magento\Store\Model\Group::CACHE_TAG),
            $this->block->getIdentities()
        );
    }
}
