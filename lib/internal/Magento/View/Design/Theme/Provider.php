<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\View\Design\Theme;

class Provider
{
    /**
     * @var \Magento\App\State
     */
    protected $appState;

    /**
     * @var FlyweightFactory
     */
    private $flyweightFactory;

    /**
     * @var ListInterface
     */
    private $themeList;

    /**
     * @param \Magento\App\State $appState
     * @param FlyweightFactory $flyweightFactory
     * @param ListInterface $themeList
     */
    public function __construct(
        \Magento\App\State $appState,
        FlyweightFactory $flyweightFactory,
        ListInterface $themeList
    ) {
        $this->appState = $appState;
        $this->flyweightFactory = $flyweightFactory;
        $this->themeList = $themeList;
    }

    /**
     * @param string $themePath
     * @param string $areaCode
     * @return \Magento\View\Design\ThemeInterface
     */
    public function getThemeModel($themePath, $areaCode)
    {
        if ($this->appState->isInstalled()) {
            $themeModel = $this->flyweightFactory->create($themePath, $areaCode);
        } else {
            $themeModel = $this->themeList->getThemeByFullPath($areaCode . '/' . $themePath);
        }
        return $themeModel;
    }
}
