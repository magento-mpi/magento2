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
     * @var \Magento\Core\Model\Store\ConfigInterface|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $storeConfigMock;

    /**
     * @var \Magento\Indexer\Model\IndexerInterface|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $flatIndexerMock;

    protected function setUp()
    {
        $this->storeConfigMock = $this->getMockForAbstractClass(
            'Magento\Core\Model\Store\ConfigInterface',
            array(), '', false, false, true, array('getConfigFlag', '__wakeup')
        );

        $this->flatIndexerMock = $this->getMockForAbstractClass(
            'Magento\Indexer\Model\IndexerInterface',
            array(), '', false, false, true, array('getId', 'getState', '__wakeup')
        );
    }

    public function testIsFlatEnabled()
    {
        $this->storeConfigMock->expects($this->once())
            ->method('getConfigFlag')
            ->with('catalog/frontend/flat_catalog_category')
            ->will($this->returnValue(true));

        $this->model = new \Magento\Catalog\Model\Indexer\Category\Flat\State(
            $this->storeConfigMock, $this->flatIndexerMock
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
        $this->flatIndexerMock->expects($this->any())
            ->method('getId')
            ->will($this->returnValue(null));
        $this->flatIndexerMock->expects($this->any())
            ->method('load')
            ->with('catalog_category_flat');
        $this->flatIndexerMock->expects($this->any())
            ->method('isValid')
            ->will($this->returnValue($isValid));

        $this->storeConfigMock->expects($this->any())
            ->method('getConfigFlag')
            ->with('catalog/frontend/flat_catalog_category')
            ->will($this->returnValue($isFlatEnabled));

        $this->model = new \Magento\Catalog\Model\Indexer\Category\Flat\State(
            $this->storeConfigMock, $this->flatIndexerMock, $isAvailable
        );
        $this->assertEquals($result, $this->model->isAvailable());
    }

    public function isAvailableDataProvider()
    {
        return array(
            array(false, true, true, false),
            array(true, false, true, false),
            array(true, true, false, false),
            array(true, true, true, true),
        );
    }
}
