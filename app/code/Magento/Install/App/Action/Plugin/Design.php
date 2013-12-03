<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Install\App\Action\Plugin;

class Design
{
    /**
     * @var \Magento\App\RequestInterface
     */
    protected $_request;

    /**
     * @var \Magento\Core\Model\App
     */
    protected $_app;

    /**
     * @var \Magento\View\LayoutInterface
     */
    protected $_layout;

    /**
     * @var \Magento\Core\Model\Theme\CollectionFactory
     */
    protected $_collectionFactory;

    /**
     * @var \Magento\View\DesignInterface
     */
    protected $_viewDesign;

    /**
     * @param \Magento\App\RequestInterface $request
     * @param \Magento\Core\Model\App $app
     * @param \Magento\View\LayoutInterface $layout
     * @param \Magento\View\DesignInterface $viewDesign
     * @param \Magento\Core\Model\Theme\CollectionFactory $collectionFactory
     */
    public function __construct(
        \Magento\App\RequestInterface $request,
        \Magento\Core\Model\App $app,
        \Magento\View\LayoutInterface $layout,
        \Magento\View\DesignInterface $viewDesign,
        \Magento\Core\Model\Theme\CollectionFactory $collectionFactory
    ) {
        $this->_viewDesign = $viewDesign;
        $this->_collectionFactory = $collectionFactory;
        $this->_request = $request;
        $this->_app = $app;
        $this->_layout = $layout;
    }

    /**
     * Initialize design
     *
     * @param array $arguments
     * @return array
     */
    public function beforeDispatch(array $arguments = array())
    {
        $areaCode = $this->_layout->getArea();
        $area = $this->_app->getArea($areaCode);
        $area->load(\Magento\Core\Model\App\Area::PART_CONFIG);

        /** @var $themesCollection \Magento\Core\Model\Theme\Collection */
        $themesCollection = $this->_collectionFactory->create();
        $themeModel = $themesCollection->addDefaultPattern($areaCode)
            ->addFilter('theme_path', $this->_viewDesign->getConfigurationDesignTheme($areaCode))
            ->getFirstItem();
//        ddd();
        $this->_viewDesign->setArea($areaCode)->setDesignTheme($themeModel);

        $area->detectDesign($this->_request);
        $area->load(\Magento\Core\Model\App\Area::PART_TRANSLATE);
        return $arguments;
    }
}
