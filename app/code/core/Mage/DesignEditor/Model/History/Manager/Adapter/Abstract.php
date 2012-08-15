<?php
/**
 * {license_notice}
 *
 * @category    Mage
 * @package     Mage_DesignEditor
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Visual design editor adapter abstract
 *
 * @method string getName()
 * @method string getHandle()
 * @method string getType()
 * @method array getActions()
 */
abstract class Mage_DesignEditor_Model_History_Manager_Adapter_Abstract extends Mage_Core_Model_Abstract
{
    /**
     * Add action to element
     *
     * @abstract
     * @param string $action
     * @param array $data
     * @return Mage_DesignEditor_Model_History_Manager_Adapter_Abstract
     */
    abstract public function addAction($action, $data);

    /**
     * Render element data
     *
     * @abstract
     * @return mixed
     */
    abstract public function render();

    /**
     * Convert element to history log
     *
     * @return array
     */
    public function toHistoryLog()
    {
        $resultData = array();

        foreach ($this->getActions() as $action => $data)
        {
            $resultData[] = array(
                'handle'       => $this->getHandle(),
                'change_type'  => $this->getType(),
                'element_name' => $this->getName(),
                'action_name'  => $action,
                'action_data'  => $data,
            );
        }

        return $resultData;
    }
}
