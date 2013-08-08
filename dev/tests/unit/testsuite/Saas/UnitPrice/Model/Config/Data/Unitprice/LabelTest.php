<?php
/**
 * {license_notice}
 *
 * @category    Saas
 * @package     unit_tests
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Unit Price Label tags stripping test
 */
class Saas_UnitPrice_Model_Config_Data_Unitprice_LabelTest extends PHPUnit_Framework_TestCase
{
    protected function eventManagerMock()
    {
        return $this->getMock('Magento_Core_Model_Event_Manager', array('dispatch'), array(), '', false);
    }

    protected function helperMock()
    {
        return $this->getMock('Saas_UnitPrice_Helper_Data', array('getConfig'), array(), '', false);
    }

    protected function cacheManagerMock()
    {
        return $this->getMock('Magento_Core_Model_CacheInterface');
    }

    protected function unitPriceMock()
    {
        return $this->getMock('Saas_UnitPrice_Model_Unitprice', array('getConversionRate'));
    }

    protected function resourceMock(Closure $saveMethod = null)
    {
        $resource = $this->getMockBuilder('Magento_Core_Model_Resource_Db_Abstract')
            ->setMethods(array(
                'save', 'beginTransaction', 'addCommitCallback', 'rollBack', 'commit',
                '_construct', 'getIdFieldName',
            ))
            ->disableOriginalConstructor()
            ->getMock();

        $resource->expects($this->any())
            ->method('addCommitCallback')
            ->will($this->returnSelf());

        if ($saveMethod) {
            $resource->expects($this->once())
                ->method('save')
                ->will($this->returnCallback($saveMethod));
        }

        return $resource;
    }

    protected function modelMock(Magento_Core_Model_Resource_Abstract $resource)
    {
        $context = new Magento_Core_Model_Context($this->eventManagerMock(), $this->cacheManagerMock());
        $model = $this->getMockBuilder('Saas_UnitPrice_Model_Config_Data_Unitprice_Label')
            ->setMethods(array('_getHelper'))
            ->setConstructorArgs(array($context, $resource))
            ->getMock();

        $model->expects($this->any())
            ->method('_getHelper')
            ->will($this->returnValue($this->helperMock()));

        $model->expects($this->any())
            ->method('_getUnitPrice')
            ->will($this->returnValue($this->unitPriceMock()));

        return $model;
    }

    public function testBeforeSaveShouldStripTagsFromValue()
    {
        // prepare
        $self = $this;
        $resource = $this->resourceMock(
            function ($label) use ($self) {
                $self->assertEquals('asdedrfwer', $label->getValue());
            }
        );
        $model = $this->modelMock($resource);

        // act
        $model->setValue('a<br/>sded<a href>rfwer');
        $model->save();
    }
}
