<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Tax\Block\Adminhtml\Rate;

use Magento\Tax\Model\Calculation\Rate;

class TitleTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var \Magento\Tax\Block\Adminhtml\Rate\Title
     */
    protected $_block;

    /**
     * @var \Magento\TestFramework\ObjectManager
     */
    protected $_objectManager;

    protected function setUp()
    {
        /** @var $objectManager \Magento\TestFramework\ObjectManager */
        $this->_objectManager = \Magento\TestFramework\Helper\Bootstrap::getObjectManager();
    }

    /**
     * @magentoDataFixture Magento/Store/_files/store.php
     * @magentoDataFixture Magento/Tax/_files/tax_classes.php
     */
    public function testGetTitles()
    {
        /** @var \Magento\Tax\Model\Calculation\Rate $rate */
        $rate = $this->_objectManager->create('Magento\Tax\Model\Calculation\Rate');
        $rate->load('*', 'code');
        /** @var \Magento\Store\Model\Store $store */
        $store = $this->_objectManager->get('\Magento\Store\Model\Store');
        $store->load('test', 'code');
        $title = 'title';
        $rate->saveTitles([$store->getId() => $title]);

        /** @var \Magento\Tax\Block\Adminhtml\Rate\Title $block */
        $block = \Magento\TestFramework\Helper\Bootstrap::getObjectManager()->create(
            'Magento\Tax\Block\Adminhtml\Rate\Title', ['rate' => $rate]
        );
        $titles = $block->getTitles();
        $this->assertArrayHasKey($store->getId(), $titles, 'Store was not created');
        $this->assertEquals($title, $titles[$store->getId()], 'Invalid Tax Title');
    }
}
