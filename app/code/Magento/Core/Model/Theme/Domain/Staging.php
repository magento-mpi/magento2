<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Core
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Staging theme model class
 */
class Magento_Core_Model_Theme_Domain_Staging
{
    /**
     * Staging theme model instance
     *
     * @var Magento_Core_Model_Theme
     */
    protected $_theme;

    /**
     * @var Magento_Core_Model_Theme_CopyService
     */
    protected $_themeCopyService;

    /**
     * @param Magento_Core_Model_Theme $theme
     * @param Magento_Core_Model_Theme_CopyService $themeCopyService
     */
    public function __construct(
        Magento_Core_Model_Theme $theme,
        Magento_Core_Model_Theme_CopyService $themeCopyService
    ) {
        $this->_theme = $theme;
        $this->_themeCopyService = $themeCopyService;
    }

    /**
     * Copy changes from 'staging' theme
     *
     * @return Magento_Core_Model_Theme_Domain_Virtual
     */
    public function updateFromStagingTheme()
    {
        $this->_themeCopyService->copy($this->_theme, $this->_theme->getParentTheme());
        return $this;
    }
}
