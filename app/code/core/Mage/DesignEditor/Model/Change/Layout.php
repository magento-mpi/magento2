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
class Mage_DesignEditor_Model_Change_Layout extends Mage_DesignEditor_Model_ChangeAbstract
{
    /**
     * Layout directives
     */
    const LAYOUT_DIRECTIVE_MOVE   = 'move';
    const LAYOUT_DIRECTIVE_REMOVE = 'remove';

    /**
     * Validate change data passed to constructor
     *
     * @throws Exception
     * @return Mage_DesignEditor_Model_ChangeAbstract|Mage_DesignEditor_Model_Change_Layout
     */
    protected function _validate()
    {
        $errors = array();
        $required = array('type', 'handle', 'change_type', 'element_name', 'action_name');

        $type = $this->getData('action_name');
        switch ($type) {
            case self::LAYOUT_DIRECTIVE_MOVE:
                $required[] = 'container';
                $required[] = 'after';
                break;

            case self::LAYOUT_DIRECTIVE_REMOVE:
                break;

            default:
                $errors[] = Mage::helper('Mage_DesignEditor_Helper_Data')->__('Invalid action name "%s"', $type);
        }

        foreach ($required as $field) {
            if (!$this->getData($field)) {
                $errors[] = Mage::helper('Mage_DesignEditor_Helper_Data')->__('Invalid "%s" data', $field);
            }
        }

        if (count($errors)) {
            throw new Exception(
                Mage::helper('Mage_DesignEditor_Helper_Data')->__('Invalid change data: %s', join(' ', $errors))
            );
        }
        return $this;
    }
}
