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
    protected $processorMock;

    /**
     * @var \Magento\Core\Model\Store|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $storeMock;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    protected $subjectMock;

    protected function setUp()
    {
        $this->processorMock = $this->getMock(
            'Magento\Catalog\Model\Indexer\Product\Flat\Processor', array('markIndexerAsInvalid'), array(), '', false
        );

        $this->subjectMock = $this->getMock('Magento\Core\Model\Resource\Store', array(), array(), '', false);
        $this->storeMock = $this->getMock(
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
        $this->processorMock->expects($this->$matcherMethod())
            ->method('markIndexerAsInvalid');

        $this->storeMock->expects($this->once())
            ->method('getId')
            ->will($this->returnValue($storeId));

        $model = new \Magento\Catalog\Model\Indexer\Product\Flat\Plugin\Store($this->processorMock);
        $model->beforeSave($this->subjectMock, $this->storeMock);
    }

    /**
     * @param string $matcherMethod
     * @param bool $storeGroupChanged
     * @dataProvider storeGroupDataProvider
     */
    public function testBeforeSaveSwitchStoreGroup($matcherMethod, $storeGroupChanged)
    {
        $this->processorMock->expects($this->$matcherMethod())
            ->method('markIndexerAsInvalid');

        $this->storeMock->expects($this->once())
            ->method('getId')
            ->will($this->returnValue(1));

        $this->storeMock->expects($this->once())
            ->method('dataHasChangedFor')->with('group_id')
            ->will($this->returnValue($storeGroupChanged));

        $model = new \Magento\Catalog\Model\Indexer\Product\Flat\Plugin\Store($this->processorMock);
        $model->beforeSave($this->subjectMock, $this->storeMock);
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
