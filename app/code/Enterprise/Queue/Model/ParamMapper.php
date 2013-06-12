<?php
/**
 * {license_notice}
 *
 * @copyright {copyright}
 * @license   {license_link}
 */

class Enterprise_Queue_Model_ParamMapper extends Mage_Core_Model_ObjectManager_ConfigAbstract
{
    /**
     * Task name prefix parameter name
     */
    const PARAM_TASK_NAME_PREFIX = 'task_name_prefix';

    /**
     * Configure di instance
     *
     * @param Magento_ObjectManager $objectManager
     */
    public function configure(Magento_ObjectManager $objectManager)
    {
        $objectManager->configure(array(
            'Enterprise_Queue_Model_Queue' => array(
                'parameters' => array('taskNamePrefix' => $this->_getParam(self::PARAM_TASK_NAME_PREFIX, ''))
            ),
        ));
    }
}
