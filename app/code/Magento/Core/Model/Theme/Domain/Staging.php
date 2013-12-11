<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Core
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Staging theme model class
 */
namespace Magento\Core\Model\Theme\Domain;

class Staging implements \Magento\View\Design\Theme\Domain\StagingInterface
{
    /**
     * Staging theme model instance
     *
     * @var \Magento\View\Design\ThemeInterface
     */
    protected $_theme;

    /**
     * @var \Magento\Theme\Model\CopyService
     */
    protected $_themeCopyService;

    /**
     * @param \Magento\View\Design\ThemeInterface $theme
     * @param \Magento\Theme\Model\CopyService $themeCopyService
     */
    public function __construct(
        \Magento\View\Design\ThemeInterface $theme,
        \Magento\Theme\Model\CopyService $themeCopyService
    ) {
        $this->_theme = $theme;
        $this->_themeCopyService = $themeCopyService;
    }

    /**
     * Copy changes from 'staging' theme
     *
     * @return \Magento\View\Design\Theme\Domain\StagingInterface
     */
    public function updateFromStagingTheme()
    {
        $this->_themeCopyService->copy($this->_theme, $this->_theme->getParentTheme());
        return $this;
    }
}
