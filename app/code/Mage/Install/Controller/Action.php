<?php
/**
 * {license_notice}
 *
 * @category    Mage
 * @package     Mage_Install
 * @copyright   {copyright}
 * @license     {license_link}
 */


class Mage_Install_Controller_Action extends Magento_Core_Controller_Varien_Action
{
    /**
     * Set current area
     */
    protected function _construct()
    {
        parent::_construct();

        $this->setCurrentArea('install');
        $this->setFlag('', self::FLAG_NO_CHECK_INSTALLATION, true);
    }

    /**
     * Initialize area and design
     *
     * @return Mage_Install_Controller_Action
     */
    protected function _initDesign()
    {
        $areaCode = $this->getLayout()->getArea();
        $area = Mage::app()->getArea($areaCode);
        $area->load(Magento_Core_Model_App_Area::PART_CONFIG)
            ->load(Magento_Core_Model_App_Area::PART_EVENTS);
        $this->_initDefaultTheme($areaCode);
        $area->detectDesign($this->getRequest());
        $area->load(Magento_Core_Model_App_Area::PART_TRANSLATE);
        return $this;
    }

    /**
     * Initialize theme
     *
     * @param string $areaCode
     * @return Mage_Install_Controller_Action
     */
    protected function _initDefaultTheme($areaCode)
    {
        /** @var $design Magento_Core_Model_View_DesignInterface */
        $design = Mage::getObjectManager()->get('Magento_Core_Model_View_DesignInterface');
        /** @var $themesCollection Magento_Core_Model_Theme_Collection */
        $themesCollection = Mage::getObjectManager()->create('Magento_Core_Model_Theme_Collection');
        $themeModel = $themesCollection->addDefaultPattern($areaCode)
            ->addFilter('theme_path', $design->getConfigurationDesignTheme($areaCode))
            ->getFirstItem();
        $design->setArea($areaCode)->setDesignTheme($themeModel);
        return $this;
    }
}
