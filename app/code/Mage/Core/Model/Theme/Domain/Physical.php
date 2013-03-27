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
 * Physical theme model class
 */
class Mage_Core_Model_Theme_Domain_Physical
{
    /**
     * Physical theme model instance
     *
     * @var Mage_Core_Model_Theme
     */
    protected $_theme;

    /**
     * @param Mage_Core_Model_Theme $theme
     */
    public function __construct(Mage_Core_Model_Theme $theme)
    {
        $this->_theme = $theme;
    }
}
