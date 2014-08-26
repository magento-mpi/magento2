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
        $this->_indexerMock->expects($this->any())->method('load')->will($this->returnValue($this->_indexerMock));

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
                'indexer' => $this->_indexerMock
            )
        );
    }

    public function testProcessValue()
    {
        $this->_indexerMock->expects($this->once())->method('invalidate');
        $this->_model->setValue('1');
        $this->_model->processValue();
    }

    public function testProcessValueNotChanged()
    {
        $this->_indexerMock->expects($this->never())->method('invalidate');
        $this->_model->processValue();
    }
}
