<?php
/**
 * {license_notice}
 *
 * @category    Saas
 * @package     unit_tests
 * @copyright   {copyright}
 * @license     {license_link}
 */

class Saas_UnitPrice_Model_Config_Data_Unitprice_UnitTest extends PHPUnit_Framework_TestCase
{
    protected function eventManagerMock()
    {
        return $this->getMock('Magento_Core_Model_Event_Manager', array('dispatch'), array(), '', false);
    }

    protected function cacheManagerMock()
    {
        return $this->getMock('Magento_Core_Model_CacheInterface');
    }

    protected function helperMock($defaultValue = 'defaut value')
    {
        $helper = $this->getMock('Saas_UnitPrice_Helper_Data', array('getConfig'), array(), '', false);
        $helper->expects($this->any())
            ->method('getConfig')
            ->will($this->returnValue($defaultValue));

        return $helper;
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

    /**
     *
     * @param Magento_Core_Model_Resource_Abstract $resource
     * @param Saas_UnitPrice_Helper_Data $helper
     * @param Saas_UnitPrice_Model_Unitprice $unitPrice
     * @param Magento_Core_Model_Event_Manager $eventManager
     * @return mixed
     *
     * @SuppressWarnings(PHPMD.NPathComplexity)
     */
    protected function modelMock(
        Magento_Core_Model_Resource_Abstract $resource = null,
        Saas_UnitPrice_Helper_Data $helper = null, Saas_UnitPrice_Model_Unitprice $unitPrice = null,
        Magento_Core_Model_Event_Manager $eventManager = null
    ) {
        $resource = $resource ?: $this->resourceMock();
        $eventManager = $eventManager ?: $this->eventManagerMock();
        $helper = $helper ?: $this->helperMock();

        $context = new Magento_Core_Model_Context($eventManager, $this->cacheManagerMock());
        $model = $this->getMock(
            'Saas_UnitPrice_Model_Config_Data_Unitprice_Unit',
            array('_getUnitPrice', '_getHelper', 'getOldValue'),
            array($context, $resource)
        );

        if ($unitPrice) {
            $model->expects($this->any())
                ->method('_getUnitPrice')
                ->will($this->returnValue($unitPrice));
        }

        if ($helper) {
            $model->expects($this->any())
                ->method('_getHelper')
                ->will($this->returnValue($helper));
        }

        return $model;
    }

    public function testOnEmptyValueSaveShouldInitializeWithDefaultValue()
    {
        // prepare
        $defaultValue = uniqid();

        $self = $this;
        $resource = $this->resourceMock(
            function ($object) use ($self, $defaultValue) {
                $self->assertEquals(array($defaultValue), $object->getValue());
            }
        );

        $model = $this->modelMock($resource, $this->helperMock($defaultValue));

        // act
        $model->setA('a'); // modify to save
        $model->save();
    }

    public function testSaveShouldFallbackToOldValueOnIncorrectUnits()
    {
        // prepare
        $oldValue = 'old value';

        $fieldSet = array(
            'default_unit_price_unit'      => $defaultUnitPrice = uniqid(),
            'default_unit_price_base_unit' => $defaultUnitBasePrice = uniqid(),
        );

        $self = $this;
        $resource = $this->resourceMock(
            function ($object) use ($self, $oldValue) {
                $self->assertEquals($oldValue, $object->getValue());
            }
        );

        $unitPrice = $this->getMock('Saas_UnitPrice_Model_Unitprice', array('getConversionRate'));
        $unitPrice->expects($this->once())
            ->method('getConversionRate')
            ->with($defaultUnitPrice, $defaultUnitBasePrice)
            ->will($this->throwException(new Exception));

        $model = $this->modelMock($resource, null, $unitPrice);
        $model->setFieldsetData($fieldSet);
        $model->expects($this->any())
            ->method('getOldValue')
            ->will($this->returnValue($oldValue));

        // act
        $model->setValue('new value');
        $model->save();
    }

    public function testGetDefaultValueShouldReturnDefaultValueFromConfig()
    {
        // prepare
        $model = $this->modelMock($this->resourceMock(), $this->helperMock($defaultValue = 'default'));

        // act & assert
        $this->assertEquals($defaultValue, $model->getDefaultValue());
    }

    public function testSaveShouldDispatchAppropriateEvents()
    {
        // prepare
        $eventManager = $this->eventManagerMock();
        $model = $this->modelMock(null, null, null, $eventManager);

        $eventManager->expects($this->at(0))
            ->method('dispatch')
            ->with('model_save_before', array('object' => $model));

        $eventManager->expects($this->at(1))
            ->method('dispatch')
            ->with('core_config_data_save_before', array('data_object' => $model, 'config_data' => $model));

        // act
        $model->setA('a');
        $model->save();
    }
}
