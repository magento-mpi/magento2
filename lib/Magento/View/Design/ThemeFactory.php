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
 * Minimal required interface a theme has to implement
 */
namespace Magento\View\Design;

use Magento\ObjectManager;
use Magento\View\Design\Theme;

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
     */
    public function getTheme($themeId)
    {
        die(__METHOD__);
    }
}
