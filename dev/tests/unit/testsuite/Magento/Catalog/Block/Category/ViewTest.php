<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Catalog\Block\Category;

class ViewTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var \Magento\Catalog\Block\Category\View
     */
    protected $block;

    protected function setUp()
    {
        $objectManager = new \Magento\TestFramework\Helper\ObjectManager($this);
        $this->block = $objectManager->getObject('Magento\Catalog\Block\Category\View');
    }

    protected function tearDown()
    {
        $this->block = null;
    }

    public function testGetIdentities()
    {
        $categoryTag = array('catalog_category_1');
        $currentCatogoryMock = $this->getMock('Magento\Catalog\Model\Category', array(), array(), '', false);
        $currentCatogoryMock->expects($this->once())
            ->method('getIdentities')
            ->will($this->returnValue($categoryTag));
        $this->block->setCurrentCategory($currentCatogoryMock);
        $this->assertEquals(
            $categoryTag,
            $this->block->getIdentities()
        );
    }
}
