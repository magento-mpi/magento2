<?php
/**
 * {license_notice}
 *
 * @category    Mage
 * @package     Mage_Install
 * @copyright   {copyright}
 * @license     {license_link}
 */


class Mage_Install_Controller_Action extends Mage_Core_Controller_Varien_Action
{
    /**
     * Currently used area
     *
     * @var string
     */
    protected $_currentArea = 'install';

    protected function _construct()
    {
        parent::_construct();
        $this->setFlag('', self::FLAG_NO_CHECK_INSTALLATION, true);
    }

    /**
     * Initialize theme
     *
     * @return Mage_Install_Controller_Action
     */
    protected function _initDefaultTheme()
    {
        $design = Mage::getDesign();
        /** @var $themesCollection Mage_Core_Model_Theme_Collection */
        $themesCollection = Mage::getModel('Mage_Core_Model_Theme_Collection');
        $themeModel = $themesCollection->addDefaultPattern($design->getArea())
            ->addFilter('theme_path', $design->getConfigurationDesignTheme($design->getArea(), array('useId' => false)))
            ->getFirstItem();
        $design->setDesignTheme($themeModel);
        return $this;
    }
}
