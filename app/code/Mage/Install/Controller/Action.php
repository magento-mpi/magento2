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
     * @var Mage_Core_Model_Config_Scope
     */
    protected $_configScope;

    /**
     * @param Mage_Core_Controller_Varien_Action_Context $context
     * @param Mage_Core_Model_Config_Scope $configScope
     */
    public function __construct(
        Mage_Core_Controller_Varien_Action_Context $context,
        Mage_Core_Model_Config_Scope $configScope
    ) {
        $this->_configScope = $configScope;
        parent::__construct($context);
    }

    protected function _construct()
    {
        parent::_construct();

        $this->_configScope->setCurrentScope('install');
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
        $area->load(Mage_Core_Model_App_Area::PART_CONFIG);
        $this->_initDefaultTheme($areaCode);
        $area->detectDesign($this->getRequest());
        $area->load(Mage_Core_Model_App_Area::PART_TRANSLATE);
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
        /** @var $design Mage_Core_Model_View_DesignInterface */
        $design = Mage::getObjectManager()->get('Mage_Core_Model_View_DesignInterface');
        /** @var $themesCollection Mage_Core_Model_Theme_Collection */
        $themesCollection = Mage::getObjectManager()->create('Mage_Core_Model_Theme_Collection');
        $themeModel = $themesCollection->addDefaultPattern($areaCode)
            ->addFilter('theme_path', $design->getConfigurationDesignTheme($areaCode))
            ->getFirstItem();
        $design->setArea($areaCode)->setDesignTheme($themeModel);
        return $this;
    }
}
