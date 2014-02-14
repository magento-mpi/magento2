<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\View\Design;

use Magento\ObjectManager;

/**
 * Class ThemeFactory
 *
 * Minimal required interface a theme has to implement
 */
class ThemeFactory
{
    /**
     * @var ObjectManager
     */
    protected $objectManager;

    /**
     * @param ObjectManager $objectManager
     */
    public function __construct(ObjectManager $objectManager)
    {
        $this->objectManager = $objectManager;
    }

    /**
     * @param int $themeId
     * @return \Magento\View\Design\ThemeInterface
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    public function getTheme($themeId)
    {
        return null;
    }
}
