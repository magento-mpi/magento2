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
 * Abstract class for preview modes
 */
abstract class Mage_DesignEditor_Model_Theme_Preview_Abstract
{
    /**
     * Theme to preview
     *
     * @var Mage_Core_Model_Theme
     */
    protected $_theme;

    /**
     * Set theme for preview
     *
     * @param Mage_Core_Model_Theme $theme
     * @return Mage_DesignEditor_Model_Theme_Preview_Default
     */
    public function setTheme(Mage_Core_Model_Theme $theme)
    {
        $this->_theme = $theme;
        return $this;
    }

    /**
     * Get current theme
     *
     * @return Mage_Core_Model_Theme
     * @throws Magento_Exception
     */
    public function getTheme()
    {
        if ($this->_theme === null) {
            throw new Magento_Exception('You need to set theme for preview');
        }
        return $this->_theme;
    }

    /**
     * Return preview url
     *
     * @abstract
     * @return string
     */
    abstract public function getPreviewUrl();
}
