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
 * Changes factory model. Creates right Change instance by given type.
 */
class Mage_DesignEditor_Model_Change_Factory
{
    /**
     * Default
     */
    const DEFAULT_TYPE = 'layout';

    /**
     * Create instance of change by given type
     *
     * @static
     * @param Varien_Object|array $change
     * @throws Magento_Exception
     * @return Mage_DesignEditor_Model_ChangeAbstract
     */
    public static function getInstance($change)
    {
        $class = self::getClass($change);
        $model = Mage::getModel($class, array('data' => $change));
        if (!$model instanceof Mage_DesignEditor_Model_ChangeAbstract) {
            throw new Magento_Exception(sprintf('Invalid change class "%s"', $class));
        }
        return $model;
    }

    /**
     * Build change class using given type
     *
     * @static
     * @param Varien_Object|Varien_Simplexml_Element|array $change
     * @return string
     */
    public static function getClass($change)
    {
        if ($change instanceof Varien_Simplexml_Element) {
            $change = self::_getAttributes($change);
        }

        $type = self::_getChangeType($change);
        if ($type == Mage_DesignEditor_Model_Change_LayoutAbstract::CHANGE_TYPE) {
            $directive = self::_getChangeLayoutDirective($change);
            $class = 'Mage_DesignEditor_Model_Change_' . ucfirst($type) . '_' . ucfirst($directive);
        } else {
            $class = 'Mage_DesignEditor_Model_Change_' . ucfirst($type);
        }

        return $class;
    }

    /**
     * Get change type
     *
     * @param Varien_Object|array $change
     * @throws Magento_Exception
     * @return string
     */
    protected static function _getChangeType($change)
    {
        $type = null;
        if (is_array($change)) {
            $type = isset($change['type']) ? $change['type'] : null;
        } elseif ($change instanceof Varien_Object) {
            $type = $change->getType();
        }

        if (!$type) {
            throw new Magento_Exception('Impossible to get change type');
        }

        return $type;
    }

    /**
     * Get change layout directive
     *
     * @param Varien_Object|array $change
     * @throws Magento_Exception
     * @return string
     */
    protected static function _getChangeLayoutDirective($change)
    {
        $directive = null;
        if (is_array($change)) {
            $directive = isset($change['action_name']) ? $change['action_name'] : null;
        } elseif ($change instanceof Varien_Object) {
            $directive = $change->getActionName();
        }

        if (!$directive) {
            throw new Magento_Exception('Impossible to get layout change directive');
        }

        return $directive;
    }

    /**
     * Load layout update attributes
     *
     * @param Varien_Simplexml_Element $layoutUpdate
     * @return array
     */
    protected static function _getAttributes(Varien_Simplexml_Element $layoutUpdate)
    {
        $attributes = array(
            'type'        => $layoutUpdate->getAttribute('type') ?: self::DEFAULT_TYPE,
            'action_name' => $layoutUpdate->getName()
        );

        return $attributes;
    }
}
