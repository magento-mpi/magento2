<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
class Magento_CatalogRule_Model_Rule_JobTest extends PHPUnit_Framework_TestCase
{
    /**
     * Test for method applyAll
     *
     * Checks that dispatch event with param value "catalogrule_apply_all" runs while applying all rules
     */
    public function testApplyAll()
    {
        $objectManagerHelper = new Magento_Test_Helper_ObjectManager($this);

        $eventManager = $this->getMock('Magento_Core_Model_Event_Manager', array(), array(), '', false);
        $eventManager->expects($this->once())
            ->method('dispatch')
            ->with($this->equalTo('catalogrule_apply_all'));

        $helper = $this->getMock('Magento_CatalogRule_Helper_Data', array(), array(), '', false);

        /** @var $jobModel Magento_CatalogRule_Model_Rule_Job */
        $jobModel = $objectManagerHelper->getObject('Magento_CatalogRule_Model_Rule_Job', array(
            'eventManager' => $eventManager,
            'helper' => $helper
        ));

        $jobModel->applyAll();
    }
}
