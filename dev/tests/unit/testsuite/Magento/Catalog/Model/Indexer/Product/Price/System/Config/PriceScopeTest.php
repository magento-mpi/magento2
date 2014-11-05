<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Catalog\Model\Indexer\Product\Price\System\Config;

class PriceScopeTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var \Magento\TestFramework\Helper\ObjectManager
     */
    protected $_objectManager;

    /**
     * @var \Magento\Catalog\Model\Indexer\Product\Price\System\Config\PriceScope
     */
    protected $_model;

    /**
     * @var \Magento\Indexer\Model\Indexer|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $_indexerMock;

    /**
     * @var \Magento\Indexer\Model\IndexerRegistry|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $indexerRegistryMock;

    public function setUp()
    {
        $this->_objectManager = new \Magento\TestFramework\Helper\ObjectManager($this);

        $this->_indexerMock = $this->getMock(
            'Magento\Indexer\Model\Indexer',
            array('load', 'invalidate'),
            array(),
            '',
            false
        );
        $this->indexerRegistryMock = $this->getMock('Magento\Indexer\Model\IndexerRegistry', ['get'], [], '', false);

        $contextMock = $this->getMock('Magento\Framework\Model\Context', array(), array(), '', false);
        $registryMock = $this->getMock('Magento\Framework\Registry', array(), array(), '', false);
        $storeManagerMock = $this->getMock('Magento\Framework\StoreManagerInterface', array(), array(), '', false);
        $configMock = $this->getMock('Magento\Framework\App\Config\ScopeConfigInterface');


        $this->_model = $this->_objectManager->getObject(
            '\Magento\Catalog\Model\Indexer\Product\Price\System\Config\PriceScope',
            array(
                'context' => $contextMock,
                'registry' => $registryMock,
                'storeManager' => $storeManagerMock,
                'config' => $configMock,
                'indexerRegistry' => $this->indexerRegistryMock
            )
        );
    }

    public function testProcessValue()
    {
        $this->_indexerMock->expects($this->once())->method('invalidate');
        $this->prepareIndexer(1);
        $this->_model->setValue('1');
        $this->_model->processValue();
    }

    public function testProcessValueNotChanged()
    {
        $this->_indexerMock->expects($this->never())->method('invalidate');
        $this->prepareIndexer(0);
        $this->_model->processValue();
    }

    /**
     * @param int $countCall
     */
    protected function prepareIndexer($countCall)
    {
        $this->indexerRegistryMock->expects($this->exactly($countCall))
            ->method('get')
            ->with(\Magento\Catalog\Model\Indexer\Product\Price\Processor::INDEXER_ID)
            ->will($this->returnValue($this->_indexerMock));
    }
}
