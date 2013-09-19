<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Install
 * @copyright   {copyright}
 * @license     {license_link}
 */


namespace Magento\Install\Controller;

class Action extends \Magento\Core\Controller\Varien\Action
{
    /**
     * @var \Magento\Core\Model\Config\Scope
     */
    protected $_configScope;

    /**
     * @var \Magento\Core\Model\View\DesignInterface
     */
    protected $_viewDesign;

    /**
     * @var \Magento\Core\Model\Theme\CollectionFactory
     */
    protected $_collectionFactory;

    /**
     * @param \Magento\Core\Controller\Varien\Action\Context $context
     * @param \Magento\Core\Model\Config\Scope $configScope
     * @param \Magento\Core\Model\View\DesignInterface $viewDesign
     * @param \Magento\Core\Model\Theme\CollectionFactory $collectionFactory
     */
    public function __construct(
        \Magento\Core\Controller\Varien\Action\Context $context,
        \Magento\Core\Model\Config\Scope $configScope,
        \Magento\Core\Model\View\DesignInterface $viewDesign,
        \Magento\Core\Model\Theme\CollectionFactory $collectionFactory
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
     * @return \Magento\Install\Controller\Action
     */
    protected function _initDesign()
    {
        $areaCode = $this->getLayout()->getArea();
        $area = \Mage::app()->getArea($areaCode);
        $area->load(\Magento\Core\Model\App\Area::PART_CONFIG);
        $this->_initDefaultTheme($areaCode);
        $area->detectDesign($this->getRequest());
        $area->load(\Magento\Core\Model\App\Area::PART_TRANSLATE);
        return $this;
    }

    /**
     * Initialize theme
     *
     * @param string $areaCode
     * @return \Magento\Install\Controller\Action
     */
    protected function _initDefaultTheme($areaCode)
    {
        /** @var $themesCollection \Magento\Core\Model\Theme\Collection */
        $themesCollection = $this->_collectionFactory->create();
        /** @var $themesCollection \Magento\Core\Model\Theme\Collection */
        $themeModel = $themesCollection->addDefaultPattern($areaCode)
            ->addFilter('theme_path', $this->_viewDesign->getConfigurationDesignTheme($areaCode))
            ->getFirstItem();
        $this->_viewDesign->setArea($areaCode)->setDesignTheme($themeModel);
        return $this;
    }
}
