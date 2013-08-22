<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_DesignEditor
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Design editor theme change
 */
class Magento_DesignEditor_Model_Theme_Change extends Magento_Core_Model_Abstract
{
    /**
     * Theme model initialization
     */
    protected function _construct()
    {
        $this->_init('Magento_DesignEditor_Model_Theme_Resource_Change');
    }

    /**
     * Load alias for theme id
     *
     * @param int $themeId
     * @return $this
     */
    public function loadByThemeId($themeId)
    {
        $this->load($themeId, 'theme_id');
        return $this;
    }
}
