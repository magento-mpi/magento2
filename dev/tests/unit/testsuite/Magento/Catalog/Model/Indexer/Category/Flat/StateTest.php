<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Catalog\Model\Indexer\Category\Flat;

class StateTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var \Magento\Catalog\Model\Indexer\Category\Flat\State
     */
    protected $model;

    /**
     * @var \Magento\App\Config\ScopeConfigInterface|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $scopeConfigMock;

    /**
     * @var \Magento\Indexer\Model\IndexerInterface|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $flatIndexerMock;

    protected function setUp()
    {
        $this->scopeConfigMock = $this->getMockForAbstractClass('Magento\App\Config\ScopeConfigInterface');

        $this->flatIndexerMock = $this->getMockForAbstractClass(
            'Magento\Indexer\Model\IndexerInterface',
            array(),
            '',
            false,
            false,
            true,
            array('getId', 'getState', '__wakeup')
        );
    }

    public function testIsFlatEnabled()
    {
        $this->scopeConfigMock->expects(
            $this->once()
        )->method(
            'isSetFlag'
        )->with(
            'catalog/frontend/flat_catalog_category'
        )->will(
            $this->returnValue(true)
        );

        $this->model = new \Magento\Catalog\Model\Indexer\Category\Flat\State(
            $this->scopeConfigMock,
            $this->flatIndexerMock
        );
        $this->assertEquals(true, $this->model->isFlatEnabled());
    }

    /**
     * @param $isAvailable
     * @param $isFlatEnabled
     * @param $isValid
     * @param $result
     * @dataProvider isAvailableDataProvider
     */
    public function testIsAvailable($isAvailable, $isFlatEnabled, $isValid, $result)
    {
        $this->flatIndexerMock->expects($this->any())->method('getId')->will($this->returnValue(null));
        $this->flatIndexerMock->expects($this->any())->method('load')->with('catalog_category_flat');
        $this->flatIndexerMock->expects($this->any())->method('isValid')->will($this->returnValue($isValid));

        $this->scopeConfigMock->expects(
            $this->any()
        )->method(
            'isSetFlag'
        )->with(
            'catalog/frontend/flat_catalog_category'
        )->will(
            $this->returnValue($isFlatEnabled)
        );

        $this->model = new \Magento\Catalog\Model\Indexer\Category\Flat\State(
            $this->scopeConfigMock,
            $this->flatIndexerMock,
            $isAvailable
        );
        $this->assertEquals($result, $this->model->isAvailable());
    }

    public function isAvailableDataProvider()
    {
        return array(
            array(false, true, true, false),
            array(true, false, true, false),
            array(true, true, false, false),
            array(true, true, true, true)
        );
    }
}
