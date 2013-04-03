<?php
/**
 * {license_notice}
 *
 * @category    Mage
 * @package     Mage_Core
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Staging theme model class
 */
class Mage_Core_Model_Theme_Domain_Staging
{
    /**
     * Staging theme model instance
     *
     * @var Mage_Core_Model_Theme
     */
    protected $_theme;

    /**
     * @var Mage_Core_Model_Theme_CopyService
     */
    protected $_themeCopyService;

    /**
     * @param Mage_Core_Model_Theme $theme
     * @param Mage_Core_Model_Theme_CopyService $themeCopyService
     */
    public function __construct(
        Mage_Core_Model_Theme $theme,
        Mage_Core_Model_Theme_CopyService $themeCopyService
    ) {
        $this->_theme = $theme;
        $this->_themeCopyService = $themeCopyService;
    }

    /**
     * Copy changes from 'staging' theme
     *
     * @return Mage_Core_Model_Theme_Domain_Virtual
     */
    public function updateFromStagingTheme()
    {
        $this->_themeCopyService->copy($this->_theme, $this->_theme->getParentTheme());
        return $this;
    }
}
