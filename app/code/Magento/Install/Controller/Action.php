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

class Action extends \Magento\App\Action\Action
{
    /**
     * @var \Magento\Config\Scope
     */
    protected $_configScope;

    /**
     * @var \Magento\View\DesignInterface
     */
    protected $_viewDesign;

    /**
     * @var \Magento\Core\Model\Theme\CollectionFactory
     */
    protected $_collectionFactory;

    /**
     * Application
     *
     * @var \Magento\Core\Model\App
     */
    protected $_app;

    /**
     * Application state
     *
     * @var \Magento\App\State
     */
    protected $_appState;

    /**
     * @param \Magento\App\Action\Context $context
     * @param \Magento\Config\Scope $configScope
     * @param \Magento\View\DesignInterface $viewDesign
     * @param \Magento\Core\Model\Theme\CollectionFactory $collectionFactory
     * @param \Magento\Core\Model\App $app
     * @param \Magento\App\State $appState
     */
    public function __construct(
        \Magento\App\Action\Context $context,
        \Magento\Config\Scope $configScope,
        \Magento\View\DesignInterface $viewDesign,
        \Magento\Core\Model\Theme\CollectionFactory $collectionFactory,
        \Magento\Core\Model\App $app,
        \Magento\App\State $appState
    ) {
        $this->_configScope = $configScope;
        $this->_viewDesign = $viewDesign;
        $this->_collectionFactory = $collectionFactory;
        parent::__construct($context);
        $this->_configScope->setCurrentScope('install');
        $this->setFlag('', self::FLAG_NO_CHECK_INSTALLATION, true);
        $this->_app = $app;
        $this->_appState = $appState;
    }

    /**
     * Initialize area and design
     *
     * @return \Magento\Install\Controller\Action
     */
    protected function _initDesign()
    {
        $areaCode = $this->getLayout()->getArea();
        $area = $this->_app->getArea($areaCode);
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
        $themeModel = $themesCollection->addDefaultPattern($areaCode)
            ->addFilter('theme_path', $this->_viewDesign->getConfigurationDesignTheme($areaCode))
            ->getFirstItem();
        $this->_viewDesign->setArea($areaCode)->setDesignTheme($themeModel);
        return $this;
    }
}
