<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Install\App\Action\Plugin;

use Magento\Framework\App\RequestInterface;

class Design
{
    /**
     * @var \Magento\Framework\App\RequestInterface
     */
    protected $_request;

    /**
     * @var \Magento\Framework\App\AreaList
     */
    protected $_areaList;

    /**
     * @var \Magento\Framework\App\State
     */
    protected $appState;

    /**
     * @var \Magento\Framework\View\Design\Theme\ListInterface
     */
    protected $_themeList;

    /**
     * @var \Magento\Framework\View\DesignInterface
     */
    protected $_viewDesign;

    /**
     * @param RequestInterface $request
     * @param \Magento\Framework\App\AreaList $areaList
     * @param \Magento\Framework\App\State $appState
     * @param \Magento\Framework\View\DesignInterface $viewDesign
     * @param \Magento\Framework\View\Design\Theme\ListInterface $themeList
     */
    public function __construct(
        \Magento\Framework\App\RequestInterface $request,
        \Magento\Framework\App\AreaList $areaList,
        \Magento\Framework\App\State $appState,
        \Magento\Framework\View\DesignInterface $viewDesign,
        \Magento\Framework\View\Design\Theme\ListInterface $themeList
    ) {
        $this->_viewDesign = $viewDesign;
        $this->_themeList = $themeList;
        $this->_request = $request;
        $this->_areaList = $areaList;
        $this->appState = $appState;
    }

    /**
     * Initialize design
     *
     * @param \Magento\Framework\App\ActionInterface $subject
     * @param RequestInterface $request
     *
     * @return void
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    public function beforeDispatch(\Magento\Framework\App\ActionInterface $subject, RequestInterface $request)
    {
        $areaCode = $this->appState->getAreaCode();
        $area = $this->_areaList->getArea($areaCode);
        $area->load(\Magento\Framework\App\Area::PART_CONFIG);

        $themePath = $this->_viewDesign->getConfigurationDesignTheme($areaCode);
        $themeFullPath = $areaCode . \Magento\Framework\View\Design\ThemeInterface::PATH_SEPARATOR . $themePath;
        $themeModel = $this->_themeList->getThemeByFullPath($themeFullPath);
        $this->_viewDesign->setDesignTheme($themeModel);

        $area->detectDesign($this->_request);
        $area->load(\Magento\Framework\App\Area::PART_TRANSLATE);
    }
}
