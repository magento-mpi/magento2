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
     * Create instance of change by given type
     *
     * @static
     * @param string $type
     * @throws Exception
     * @return Mage_DesignEditor_Model_ChangeAbstract
     */
    public static function getInstance($type)
    {
        $model = Mage::getModel(self::getClass($type));
        if (!$model instanceof Mage_DesignEditor_Model_ChangeAbstract) {
            throw new Exception(
                Mage::helper('Mage_DesignEditor_Helper_Data')->__('Invalid change type "%s"', $type)
            );
        }
        return $model;
    }

    /**
     * Build change class using given type
     *
     * @static
     * @param string $type
     * @return string
     */
    public static function getClass($type)
    {
        return 'Mage_DesignEditor_Model_Change_' . ucfirst($type);
    }
}
