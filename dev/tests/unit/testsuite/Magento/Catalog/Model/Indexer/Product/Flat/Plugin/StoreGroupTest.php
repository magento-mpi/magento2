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
    protected $_processorMock;

    /**
     * @var \Magento\Core\Model\Store\Group|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $_storeGroupMock;

    protected function setUp()
    {
        $this->_processorMock = $this->getMock(
            'Magento\Catalog\Model\Indexer\Product\Flat\Processor', array('markIndexerAsInvalid'), array(), '', false
        );

        $this->_storeGroupMock = $this->getMock(
            'Magento\Core\Model\Store\Group', array('getId', '__wakeup', 'dataHasChangedFor'), array(), '', false
        );
    }

    /**
     * @param string $matcherMethod
     * @param int|null $storeId
     * @dataProvider storeGroupDataProvider
     */
    public function testBeforeSave($matcherMethod, $storeId)
    {
        $this->_processorMock->expects($this->$matcherMethod())
            ->method('markIndexerAsInvalid');

        $this->_storeGroupMock->expects($this->once())
            ->method('getId')
            ->will($this->returnValue($storeId));

        $methodArguments = array($this->_storeGroupMock);

        $model = new \Magento\Catalog\Model\Indexer\Product\Flat\Plugin\StoreGroup($this->_processorMock);
        $this->assertEquals($methodArguments, $model->beforeSave($methodArguments));
    }

    /**
     * @param string $matcherMethod
     * @param bool $websiteChanged
     * @dataProvider storeGroupWebsiteDataProvider
     */
    public function testChangedWebsiteBeforeSave($matcherMethod, $websiteChanged)
    {
        $this->_processorMock->expects($this->$matcherMethod())
            ->method('markIndexerAsInvalid');

        $this->_storeGroupMock->expects($this->once())
            ->method('getId')
            ->will($this->returnValue(1));

        $this->_storeGroupMock->expects($this->once())
            ->method('dataHasChangedFor')->with('root_category_id')
            ->will($this->returnValue($websiteChanged));

        $methodArguments = array($this->_storeGroupMock);

        $model = new \Magento\Catalog\Model\Indexer\Product\Flat\Plugin\StoreGroup($this->_processorMock);
        $this->assertEquals($methodArguments, $model->beforeSave($methodArguments));
    }

    /**
     * @return array
     */
    public function storeGroupWebsiteDataProvider()
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
    public function storeGroupDataProvider()
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
