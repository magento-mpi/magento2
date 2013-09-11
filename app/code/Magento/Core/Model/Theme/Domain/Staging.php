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

class Staging
{
    /**
     * Staging theme model instance
     *
     * @var \Magento\Core\Model\Theme
     */
    protected $_theme;

    /**
     * @var \Magento\Core\Model\Theme\CopyService
     */
    protected $_themeCopyService;

    /**
     * @param \Magento\Core\Model\Theme $theme
     * @param \Magento\Core\Model\Theme\CopyService $themeCopyService
     */
    public function __construct(
        \Magento\Core\Model\Theme $theme,
        \Magento\Core\Model\Theme\CopyService $themeCopyService
    ) {
        $this->_theme = $theme;
        $this->_themeCopyService = $themeCopyService;
    }

    /**
     * Copy changes from 'staging' theme
     *
     * @return \Magento\Core\Model\Theme\Domain\Virtual
     */
    public function updateFromStagingTheme()
    {
        $this->_themeCopyService->copy($this->_theme, $this->_theme->getParentTheme());
        return $this;
    }
}
