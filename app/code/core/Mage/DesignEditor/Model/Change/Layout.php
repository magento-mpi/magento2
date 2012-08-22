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
 * Layout change model
 */
abstract class Mage_DesignEditor_Model_Change_Layout extends Mage_DesignEditor_Model_ChangeAbstract
{
    const CHANGE_TYPE = 'layout';

    /**
     * Validate layout move change data passed to constructor
     *
     * @throws Mage_Core_Exception
     * @return Mage_DesignEditor_Model_ChangeAbstract|Mage_DesignEditor_Model_Change_Layout
     */
    protected function _validate()
    {
        $errors = array();
        foreach ($this->_getRequiredFields() as $field) {
            if (!$this->getData($field)) {
                $errors[] = Mage::helper('Mage_DesignEditor_Helper_Data')->__('Invalid "%s" data', $field);
            }
        }

        if (count($errors)) {
            Mage::throwException(
                Mage::helper('Mage_DesignEditor_Helper_Data')->__('Invalid change data: %s', join(' ', $errors))
            );
        }
        return $this;
    }

    abstract public function getLayoutUpdateData();
    
    /**
     * Get required data fields for layout change
     *
     * @return array
     */
    protected function _getRequiredFields()
    {
        return array('type', 'handle', 'change_type', 'element_name', 'action_name');
    }

    /**
     * Get layout update directive for given layout change
     *
     * @return string
     */
    public function getLayoutDirective()
    {
        return self::LAYOUT_DIRECTIVE;
    }
}
