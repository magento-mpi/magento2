<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Install\App\Action\Plugin;
use Magento\App\RequestInterface;

class Design
{
    /**
     * @var \Magento\App\RequestInterface
     */
    protected $_request;

    /**
     * @var \Magento\App\AreaList
     */
    protected $_areaList;

    /**
     * @var \Magento\View\LayoutInterface
     */
    protected $_layout;

    /**
     * @var \Magento\View\Design\Theme\ListInterface
     */
    protected $_themeList;

    /**
     * @var \Magento\View\DesignInterface
     */
    protected $_viewDesign;

    /**
     * @param RequestInterface $request
     * @param \Magento\App\AreaList $areaList
     * @param \Magento\View\LayoutInterface $layout
     * @param \Magento\View\DesignInterface $viewDesign
     * @param \Magento\View\Design\Theme\ListInterface $themeList
     */
    public function __construct(
        \Magento\App\RequestInterface $request,
        \Magento\App\AreaList $areaList,
        \Magento\View\LayoutInterface $layout,
        \Magento\View\DesignInterface $viewDesign,
        \Magento\View\Design\Theme\ListInterface $themeList
    ) {
        $this->_viewDesign = $viewDesign;
        $this->_themeList = $themeList;
        $this->_request = $request;
        $this->_areaList = $areaList;
        $this->_layout = $layout;
    }

    /**
     * Initialize design
     *
     * @param \Magento\Install\Controller\Action $subject
     * @param RequestInterface $request
     *
     * @return void
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    public function beforeDispatch(\Magento\Install\Controller\Action $subject, RequestInterface $request)
    {
        $areaCode = $this->_layout->getArea();
        $area = $this->_areaList->getArea($areaCode);
        $area->load(\Magento\Core\Model\App\Area::PART_CONFIG);

        $themePath = $this->_viewDesign->getConfigurationDesignTheme($areaCode);
        $themeFullPath = $areaCode . \Magento\View\Design\ThemeInterface::PATH_SEPARATOR . $themePath;
        $themeModel = $this->_themeList->getThemeByFullPath($themeFullPath);
        $this->_viewDesign->setArea($areaCode)->setDesignTheme($themeModel);

        $area->detectDesign($this->_request);
        $area->load(\Magento\Core\Model\App\Area::PART_TRANSLATE);
    }
}
