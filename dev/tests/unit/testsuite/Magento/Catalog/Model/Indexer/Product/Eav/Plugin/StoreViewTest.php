<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Catalog\Model\Indexer\Product\Eav\Plugin;

class StoreViewTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @param array $data
     * @dataProvider beforeSaveDataProvider
     */
    public function testBeforeSave(array $data)
    {
        $eavProcessorMock = $this->getMockBuilder('Magento\Catalog\Model\Indexer\Product\Eav\Processor')
            ->disableOriginalConstructor()
            ->getMock();
        $matcher = $data['matcher'];
        $eavProcessorMock->expects($this->$matcher())
            ->method('markIndexerAsInvalid');

        $subjectMock = $this->getMockBuilder('Magento\Store\Model\Resource\Store')
            ->disableOriginalConstructor()
            ->getMock();

        $objectMock = $this->getMockBuilder('Magento\Framework\Model\AbstractModel')
            ->disableOriginalConstructor()
            ->setMethods(array('getId', 'dataHasChangedFor', 'getIsActive', '__wakeup'))
            ->getMock();
        $objectMock->expects($this->any())
            ->method('getId')
            ->will($this->returnValue($data['object_id']));
        $objectMock->expects($this->any())
            ->method('dataHasChangedFor')
            ->with('group_id')
            ->will($this->returnValue($data['has_group_id_changed']));
        $objectMock->expects($this->any())
            ->method('getIsActive')
            ->will($this->returnValue($data['is_active']));

        $model = new \Magento\Catalog\Model\Indexer\Product\Eav\Plugin\StoreView($eavProcessorMock);
        $model->beforeSave($subjectMock, $objectMock);
    }

    /**
     * @return array
     */
    public function beforeSaveDataProvider()
    {
        return array(
            array(
                array(
                    'matcher' => 'once',
                    'object_id' => 1,
                    'has_group_id_changed' => true,
                    'is_active' => true
                )
            ),
            array(
                array(
                    'matcher' => 'never',
                    'object_id' => 1,
                    'has_group_id_changed' => false,
                    'is_active' => true
                )
            ),
            array(
                array(
                    'matcher' => 'never',
                    'object_id' => 1,
                    'has_group_id_changed' => true,
                    'is_active' => false
                )
            ),
            array(
                array(
                    'matcher' => 'never',
                    'object_id' => 1,
                    'has_group_id_changed' => false,
                    'is_active' => false
                )
            ),
            array(
                array(
                    'matcher' => 'once',
                    'object_id' => 0,
                    'has_group_id_changed' => true,
                    'is_active' => true
                )
            ),
            array(
                array(
                    'matcher' => 'once',
                    'object_id' => 0,
                    'has_group_id_changed' => false,
                    'is_active' => true
                )
            ),
            array(
                array(
                    'matcher' => 'never',
                    'object_id' => 0,
                    'has_group_id_changed' => true,
                    'is_active' => false
                )
            ),
            array(
                array(
                    'matcher' => 'never',
                    'object_id' => 0,
                    'has_group_id_changed' => false,
                    'is_active' => false
                )
            ),
        );
    }
}
