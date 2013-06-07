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
 * Design editor theme change
 */
class Mage_DesignEditor_Model_Theme_Change extends Mage_Core_Model_Abstract
{
    /**
     * Theme model initialization
     */
    protected function _construct()
    {
        $this->_init('Mage_DesignEditor_Model_Theme_Resource_Change');
    }
}
