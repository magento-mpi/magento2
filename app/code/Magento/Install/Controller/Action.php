<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Install
 * @copyright   {copyright}
 * @license     {license_link}
 */


class Magento_Install_Controller_Action extends Magento_Core_Controller_Varien_Action
{
    /**
     * @var Magento_Core_Model_Config_Scope
     */
    protected $_configScope;

    /**
     * @var Magento_Core_Model_View_DesignInterface
     */
    protected $_viewDesign;

    /**
     * @var Magento_Core_Model_Theme_CollectionFactory
     */
    protected $_collectionFactory;

    /**
     * @param Magento_Core_Controller_Varien_Action_Context $context
     * @param Magento_Core_Model_Config_Scope $configScope
     * @param Magento_Core_Model_View_DesignInterface $viewDesign
     * @param Magento_Core_Model_Theme_CollectionFactory $collectionFactory
     */
    public function __construct(
        Magento_Core_Controller_Varien_Action_Context $context,
        Magento_Core_Model_Config_Scope $configScope,
        Magento_Core_Model_View_DesignInterface $viewDesign,
        Magento_Core_Model_Theme_CollectionFactory $collectionFactory
    ) {
        $this->_configScope = $configScope;
        $this->_viewDesign = $viewDesign;
        $this->_collectionFactory = $collectionFactory;
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
     * @return Magento_Install_Controller_Action
     */
    protected function _initDesign()
    {
        $areaCode = $this->getLayout()->getArea();
        $area = Mage::app()->getArea($areaCode);
        $area->load(Magento_Core_Model_App_Area::PART_CONFIG);
        $this->_initDefaultTheme($areaCode);
        $area->detectDesign($this->getRequest());
        $area->load(Magento_Core_Model_App_Area::PART_TRANSLATE);
        return $this;
    }

    /**
     * Initialize theme
     *
     * @param string $areaCode
     * @return Magento_Install_Controller_Action
     */
    protected function _initDefaultTheme($areaCode)
    {
        /** @var $themesCollection Magento_Core_Model_Theme_Collection */
        $themesCollection = $this->_collectionFactory->create();
        $themeModel = $themesCollection->addDefaultPattern($areaCode)
            ->addFilter('theme_path', $this->_viewDesign->getConfigurationDesignTheme($areaCode))
            ->getFirstItem();
        $this->_viewDesign->setArea($areaCode)->setDesignTheme($themeModel);
        return $this;
    }
}
