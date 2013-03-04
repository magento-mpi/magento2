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
     * @param Mage_Core_Model_Theme $theme
     * @param array $data
     */
    public function __construct(
        Mage_Core_Model_Theme $theme,
        array $data = array()
    ) {
        $this->_theme = $theme;
    }
}
