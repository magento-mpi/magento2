<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Core\Model\Theme;

/**
 * Theme resolver model
 */
class Resolver implements \Magento\View\Design\Theme\ResolverInterface
{
    /**
     * @var \Magento\View\DesignInterface
     */
    protected $design;

    /**
     * @var \Magento\Core\Model\Resource\Theme\CollectionFactory
     */
    protected $themeFactory;

    /**
     * @var \Magento\Framework\App\State
     */
    protected $appState;

    /**
     * @param \Magento\Framework\App\State $appState
     * @param \Magento\View\DesignInterface $design
     * @param \Magento\Core\Model\Resource\Theme\CollectionFactory $themeFactory
     */
    public function __construct(
        \Magento\Framework\App\State $appState,
        \Magento\View\DesignInterface $design,
        \Magento\Core\Model\Resource\Theme\CollectionFactory $themeFactory
    ) {
        $this->design = $design;
        $this->themeFactory = $themeFactory;
        $this->appState = $appState;
    }

    /**
     * Retrieve instance of a theme currently used in an area
     *
     * @return \Magento\View\Design\ThemeInterface
     */
    public function get()
    {
        $area = $this->appState->getAreaCode();
        if ($this->design->getDesignTheme()->getArea() == $area || $this->design->getArea() == $area) {
            return $this->design->getDesignTheme();
        }

        /** @var \Magento\Core\Model\Resource\Theme\Collection $themeCollection */
        $themeCollection = $this->themeFactory->create();
        $themeIdentifier = $this->design->getConfigurationDesignTheme($area);
        if (is_numeric($themeIdentifier)) {
            $result = $themeCollection->getItemById($themeIdentifier);
        } else {
            $themeFullPath = $area . \Magento\View\Design\ThemeInterface::PATH_SEPARATOR . $themeIdentifier;
            $result = $themeCollection->getThemeByFullPath($themeFullPath);
        }
        return $result;
    }
}
