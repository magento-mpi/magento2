<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Cms\Block;

class BlockTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var \Magento\Cms\Block\Block
     */
    protected $block;

    protected function setUp()
    {
        $objectManager = new \Magento\TestFramework\Helper\ObjectManager($this);
        $this->block = $objectManager->getObject('Magento\Cms\Block\Block');
    }

    protected function tearDown()
    {
        $this->block = null;
    }

    public function testGetIdentities()
    {
        $id = 1;
        $this->block->setBlockId($id);
        $this->assertEquals(
            array(\Magento\Cms\Model\Block::CACHE_TAG . '_' . $id),
            $this->block->getIdentities()
        );
    }
}
