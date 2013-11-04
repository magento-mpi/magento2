<?php
/**
 * Minimal required interface a theme has to implement
 *
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\View\Design;

use Magento\ObjectManager;

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
     * @param $themeId
     * @return \Magento\View\Design\ThemeInterface
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    public function getTheme($themeId)
    {
        return null;
    }
}
