<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Catalog\Model\Indexer\Product\Flat;

class StateTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var \Magento\TestFramework\Helper\ObjectManager
     */
    protected $_objectManager;

    /**
     * @var \Magento\Catalog\Model\Indexer\Product\Flat\State
     */
    protected $_model;

    public function setUp()
    {
        $this->_objectManager = new \Magento\TestFramework\Helper\ObjectManager($this);

        $indexerMock = $this->getMock('Magento\Indexer\Model\Indexer', array(), array(), '', false);
        $flatIndexerHelperMock = $this->getMock(
            'Magento\Catalog\Helper\Product\Flat\Indexer',
            array(),
            array(),
            '',
            false
        );
        $configMock = $this->getMock('Magento\Framework\App\Config\ScopeConfigInterface');
        $this->_model = $this->_objectManager->getObject(
            'Magento\Catalog\Model\Indexer\Product\Flat\State',
            array(
                'scopeConfig' => $configMock,
                'flatIndexer' => $indexerMock,
                'flatIndexerHelper' => $flatIndexerHelperMock,
                false
            )
        );
    }

    public function testGetIndexer()
    {
        $this->assertInstanceOf('\Magento\Catalog\Helper\Product\Flat\Indexer', $this->_model->getFlatIndexerHelper());
    }
}
