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
     * Model to update current 'virtual' theme with changes taken form associated 'staging' theme
     *
     * @var Mage_Core_Model_Theme_Copy_StagingToVirtual
     */
    protected $_copyModelSV;

    /**
     * @param Mage_Core_Model_Theme $theme
     * @param Mage_Core_Model_Theme_Copy_StagingToVirtual $copyModelSV
     * @param array $data
     */
    public function __construct(
        Mage_Core_Model_Theme $theme,
        Mage_Core_Model_Theme_Copy_StagingToVirtual $copyModelSV,
        array $data = array()
    ) {
        $this->_theme = $theme;
        $this->_copyModelSV = $copyModelSV;
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
}
