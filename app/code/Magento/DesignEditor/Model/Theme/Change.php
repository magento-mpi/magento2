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
namespace Magento\DesignEditor\Model\Theme;

class Change extends \Magento\Core\Model\AbstractModel
{
    /**
     * Theme model initialization
     */
    protected function _construct()
    {
        $this->_init('\Magento\DesignEditor\Model\Theme\Resource\Change');
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
