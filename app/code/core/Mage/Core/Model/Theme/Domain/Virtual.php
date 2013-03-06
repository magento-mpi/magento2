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
 * Virtual theme domain model
 */
class Mage_Core_Model_Theme_Domain_Virtual
{
    /**
     * Virtual theme model instance
     *
     * @var Mage_Core_Model_Theme
     */
    protected $_theme;

    /**
     * Model to create 'staging' copy of current 'virtual' theme
     *
     * @var Mage_Core_Model_Theme_Copy_VirtualToStaging
     */
    protected $_copyModel;

    /**
     * @param Mage_Core_Model_Theme $theme
     * @param Mage_Core_Model_Theme_Copy_VirtualToStaging $copyModel
     * @param array $data
     *
     */
    public function __construct(
        Mage_Core_Model_Theme $theme,
        Mage_Core_Model_Theme_Copy_VirtualToStaging $copyModel,
        array $data = array()
    ) {
        $this->_theme = $theme;
        $this->_copyModel = $copyModel;
    }

    /**
     * Get 'staging' theme
     *
     * @return Mage_Core_Model_Theme
     */
    public function getStagingTheme()
    {
        return $this->_hasStagingTheme() ? $this->_getStagingTheme() : $this->createStagingTheme();
    }

    /**
     * Check if theme has associated 'staging' theme
     *
     * @return bool
     */
    protected function _hasStagingTheme()
    {
        return (bool)$this->_getStagingTheme()->getId();
    }

    /**
     * Get staging theme
     *
     * @return Mage_Core_Model_Theme
     */
    protected function _getStagingTheme()
    {
        return $this->_theme->getCollection()
            ->addFieldToFilter('parent_id', $this->_theme->getId())
            ->addFieldToFilter('type', Mage_Core_Model_Theme::TYPE_STAGING)
            ->getFirstItem();
    }

    /**
     * Create 'staging' theme associated with current 'virtual' theme
     *
     * @return Mage_Core_Model_Theme
     */
    public function createStagingTheme()
    {
        return $this->_copyModel->copy($this->_theme);
    }
}
