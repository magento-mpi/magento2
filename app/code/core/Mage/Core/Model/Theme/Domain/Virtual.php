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
     * Staging theme model instance
     *
     * @var Mage_Core_Model_Theme
     */
    protected $_stagingTheme;

    /**
     * Model to create 'staging' copy of current 'virtual' theme
     *
     * @var Mage_Core_Model_Theme_Copy_VirtualToStaging
     */
    protected $_copyModelVS;

    /**
     * Model to update current 'virtual' theme with changes taken form associated 'staging' theme
     *
     * @var Mage_Core_Model_Theme_Copy_StagingToVirtual
     */
    protected $_copyModelSV;

    /**
     * @param Mage_Core_Model_Theme $theme
     * @param Mage_Core_Model_Theme_Copy_VirtualToStaging $copyModelVS
     * @param Mage_Core_Model_Theme_Copy_StagingToVirtual $copyModelSV
     * @param array $data
     */
    public function __construct(
        Mage_Core_Model_Theme $theme,
        Mage_Core_Model_Theme_Copy_VirtualToStaging $copyModelVS,
        Mage_Core_Model_Theme_Copy_StagingToVirtual $copyModelSV,
        array $data = array()
    ) {
        $this->_theme = $theme;
        $this->_copyModelVS = $copyModelVS;
        $this->_copyModelSV = $copyModelSV;
    }

    /**
     * Get 'staging' theme
     *
     * @return Mage_Core_Model_Theme
     */
    public function getStagingTheme()
    {
        if (!$this->_stagingTheme) {
            $stagingTheme = $this->_getStagingTheme();
            $this->_stagingTheme =  $stagingTheme->getId() ? $stagingTheme : $this->_createStagingTheme();
        }
        return $this->_stagingTheme;
    }

    /**
     * Copy changes from 'staging' theme
     *
     * @return Mage_Core_Model_Theme_Domain_Virtual
     */
    public function updateFromStagingTheme()
    {
        $this->_copyModelSV->copy($this->_theme);

        return $this;
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
    protected function _createStagingTheme()
    {
        return $this->_copyModelVS->copy($this->_theme);
    }
}
