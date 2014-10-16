<?php
/**
 * {license_notice}
 *
 * @copyright {copyright}
 * @license {license_link}
 */
namespace Magento\CatalogRule\Plugin\Indexer\Product;

use Magento\TestFramework\Helper\ObjectManager;

class PriceIndexerTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var \Magento\Catalog\Model\Indexer\Product\Price\Processor|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $priceProcessor;

    /**
     * @var \Magento\Catalog\Model\Category|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $subject;

    /**
     * @var \Magento\CatalogRule\Plugin\Indexer\Product\PriceIndexer
     */
    protected $plugin;

    protected function setUp()
    {
        $this->priceProcessor = $this->getMock('Magento\Catalog\Model\Indexer\Product\Price\Processor', [], [], '',
            false);
        $this->subject = $this->getMock('Magento\CatalogRule\Model\Indexer\IndexBuilder', [], [], '', false);

        $this->plugin = (new ObjectManager($this))->getObject(
            'Magento\CatalogRule\Plugin\Indexer\Product\PriceIndexer',
            [
                'priceProcessor' => $this->priceProcessor,
            ]
        );
    }

    public function testAfterSaveWithoutAffectedProductIds()
    {
        $this->priceProcessor->expects($this->once())->method('markIndexerAsInvalid');

        $this->plugin->afterReindexFull($this->subject, $this->subject);
    }
}
