<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\CatalogRule\Model\Rule;

class JobTest extends \PHPUnit_Framework_TestCase
{
    /**
     * Test for method applyAll
     *
     * Checks that dispatch event with param value "catalogrule_apply_all" runs while applying all rules
     */
    public function testApplyAll()
    {
        $objectManagerHelper = new \Magento\TestFramework\Helper\ObjectManager($this);

        $eventManager = $this->getMock('Magento\Framework\Event\ManagerInterface', array(), array(), '', false);
        $eventManager->expects($this->never())->method('dispatch')->with($this->equalTo('catalogrule_apply_all'));

        /** @var $jobModel \Magento\CatalogRule\Model\Rule\Job */
        $jobModel = $objectManagerHelper->getObject(
            'Magento\CatalogRule\Model\Rule\Job',
            array('eventManager' => $eventManager)
        );

        $jobModel->applyAll();
    }
}
