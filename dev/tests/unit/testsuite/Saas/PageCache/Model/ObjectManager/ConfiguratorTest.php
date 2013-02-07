<?php
/**
 * {license_notice}
 *
 * @category    Saas
 * @package     unit_tests
 * @copyright   {copyright}
 * @license     {license_link}
 */

class Saas_PageCache_Model_ObjectManager_ConfiguratorTest extends PHPUnit_Framework_TestCase
{
    public function testConfigure()
    {
        $params = array(
            'MAGE_RUN_CODE' => 'run_code',
        );
        $model = new Saas_PageCache_Model_ObjectManager_Configurator($params);
        /** @var $objectManagerMock PHPUnit_Framework_MockObject_MockObject */
        $objectManagerMock = $this->getMock('Magento_ObjectManager');

        $exceptedConfig = array(
            'preference' => array(
                'Enterprise_PageCache_Model_Processor_RestrictionInterface'
                    => 'Saas_PageCache_Model_Processor_Restriction',
                'Enterprise_PageCache_Model_Processor' => 'Saas_PageCache_Model_Processor',
            ),
            'Saas_PageCache_Model_Processor' => array(
                'parameters' => array(
                    'scopeCode' => 'run_code',
                )
            ),
        );
        
        $objectManagerMock->expects($this->once())
            ->method('setConfiguration')
            ->with($exceptedConfig);

        $model->configure($objectManagerMock);
    }
}
