<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Theme\Block\Html;

class FooterTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var \Magento\Theme\Block\Html\Footer
     */
    protected $block;

    protected function setUp()
    {
        $objectManager = new \Magento\TestFramework\Helper\ObjectManager($this);
        $this->block = $objectManager->getObject('Magento\Theme\Block\Html\Footer');
    }

    protected function tearDown()
    {
        $this->block = null;
    }

    public function testGetIdentities()
    {
        $this->assertEquals(
            array(\Magento\Store\Model\Store::CACHE_TAG, \Magento\Cms\Model\Block::CACHE_TAG),
            $this->block->getIdentities()
        );
    }
}
