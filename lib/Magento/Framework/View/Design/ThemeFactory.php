<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Framework\View\Design;

use Magento\Framework\ObjectManager;

/**
 * Class ThemeFactory
 *
 * Minimal required interface a theme has to implement
 */
class ThemeFactory
{
    /**
     * Object manager
     *
     * @var ObjectManager
     */
    protected $objectManager;

    /**
     * Constructor
     *
     * @param ObjectManager $objectManager
     */
    public function __construct(ObjectManager $objectManager)
    {
        $this->objectManager = $objectManager;
    }

    /**
     * Get theme
     *
     * @param int $themeId
     * @return null|\Magento\Framework\View\Design\ThemeInterface
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    public function getTheme($themeId)
    {
        return null;
    }
}
