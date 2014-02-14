<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Catalog
 * @subpackage  unit_tests
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Catalog\Model\Indexer\Product\Flat\Plugin;

class StoreTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var \Magento\Catalog\Model\Indexer\Product\Flat\Processor|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $_processorMock;

    /**
     * @var \Magento\Core\Model\Store|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $_storeMock;

    protected function setUp()
    {
        $this->_processorMock = $this->getMock(
            'Magento\Catalog\Model\Indexer\Product\Flat\Processor', array('markIndexerAsInvalid'), array(), '', false
        );

        $this->_storeMock = $this->getMock(
            'Magento\Core\Model\Store', array('getId', '__wakeup', 'dataHasChangedFor'), array(), '', false
        );
    }

    /**
     * @param string $matcherMethod
     * @param int|null $storeId
     * @dataProvider storeDataProvider
     */
    public function testBeforeSave($matcherMethod, $storeId)
    {
        $this->_processorMock->expects($this->$matcherMethod())
            ->method('markIndexerAsInvalid');

        $this->_storeMock->expects($this->once())
            ->method('getId')
            ->will($this->returnValue($storeId));

        $methodArguments = array($this->_storeMock);

        $model = new \Magento\Catalog\Model\Indexer\Product\Flat\Plugin\Store($this->_processorMock);
        $this->assertEquals($methodArguments, $model->beforeSave($methodArguments));
    }

    /**
     * @param string $matcherMethod
     * @param bool $storeGroupChanged
     * @dataProvider storeGroupDataProvider
     */
    public function testBeforeSaveSwitchStoreGroup($matcherMethod, $storeGroupChanged)
    {
        $this->_processorMock->expects($this->$matcherMethod())
            ->method('markIndexerAsInvalid');

        $this->_storeMock->expects($this->once())
            ->method('getId')
            ->will($this->returnValue(1));

        $this->_storeMock->expects($this->once())
            ->method('dataHasChangedFor')->with('group_id')
            ->will($this->returnValue($storeGroupChanged));

        $methodArguments = array($this->_storeMock);

        $model = new \Magento\Catalog\Model\Indexer\Product\Flat\Plugin\Store($this->_processorMock);
        $this->assertEquals($methodArguments, $model->beforeSave($methodArguments));
    }
    /**
     * @return array
     */
    public function storeGroupDataProvider()
    {
        return array(
            array(
                'once', true
            ),
            array(
                'never', false
            )
        );
    }

    /**
     * @return array
     */
    public function storeDataProvider()
    {
        return array(
            array(
                'once',
                null
            ),
            array(
                'never',
                1
            )
        );
    }
}
