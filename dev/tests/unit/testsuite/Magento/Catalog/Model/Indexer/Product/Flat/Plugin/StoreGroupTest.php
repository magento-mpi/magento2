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

class StoreGroupTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var \Magento\Catalog\Model\Indexer\Product\Flat\Processor|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $processorMock;

    /**
     * @var \Magento\Store\Model\Store|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $storeGroupMock;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    protected $subjectMock;

    protected function setUp()
    {
        $this->processorMock = $this->getMock(
            'Magento\Catalog\Model\Indexer\Product\Flat\Processor',
            array('markIndexerAsInvalid'),
            array(),
            '',
            false
        );

        $this->subjectMock = $this->getMock('Magento\Store\Model\Resource\Group', array(), array(), '', false);
        $this->storeGroupMock = $this->getMock(
            'Magento\Store\Model\Group',
            array('getId', '__wakeup', 'dataHasChangedFor'),
            array(),
            '',
            false
        );
    }

    /**
     * @param string $matcherMethod
     * @param int|null $storeId
     * @dataProvider storeGroupDataProvider
     */
    public function testBeforeSave($matcherMethod, $storeId)
    {
        $this->processorMock->expects($this->{$matcherMethod}())->method('markIndexerAsInvalid');

        $this->storeGroupMock->expects($this->once())->method('getId')->will($this->returnValue($storeId));

        $model = new \Magento\Catalog\Model\Indexer\Product\Flat\Plugin\StoreGroup($this->processorMock);
        $model->beforeSave($this->subjectMock, $this->storeGroupMock);
    }

    /**
     * @param string $matcherMethod
     * @param bool $websiteChanged
     * @dataProvider storeGroupWebsiteDataProvider
     */
    public function testChangedWebsiteBeforeSave($matcherMethod, $websiteChanged)
    {
        $this->processorMock->expects($this->{$matcherMethod}())->method('markIndexerAsInvalid');

        $this->storeGroupMock->expects($this->once())->method('getId')->will($this->returnValue(1));

        $this->storeGroupMock->expects(
            $this->once()
        )->method(
            'dataHasChangedFor'
        )->with(
            'root_category_id'
        )->will(
            $this->returnValue($websiteChanged)
        );

        $model = new \Magento\Catalog\Model\Indexer\Product\Flat\Plugin\StoreGroup($this->processorMock);
        $model->beforeSave($this->subjectMock, $this->storeGroupMock);
    }

    /**
     * @return array
     */
    public function storeGroupWebsiteDataProvider()
    {
        return array(array('once', true), array('never', false));
    }

    /**
     * @return array
     */
    public function storeGroupDataProvider()
    {
        return array(array('once', null), array('never', 1));
    }
}
