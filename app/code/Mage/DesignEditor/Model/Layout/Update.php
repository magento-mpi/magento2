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
 * VDE Layout Update model class
 *
 * @method string getIsVde() getIsVde()
 * @method Mage_DesignEditor_Model_Layout_Update setIsVde() setIsVde(string $flag)
 */
class Mage_DesignEditor_Model_Layout_Update extends Mage_Core_Model_Layout_Update
{
    /**
     * Layout Update model initialization
     */
    protected function _construct()
    {
        $this->_init('Mage_DesignEditor_Model_Resource_Layout_Update');
    }

    /**
     * Set true for flag is_vde
     *
     * @return Mage_DesignEditor_Model_Layout_Update
     */
    protected function _beforeSave()
    {
        $this->setIsVde(true);
        return parent::_beforeSave();
    }
}
