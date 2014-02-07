<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\View\Layout\File;

use Magento\ObjectManager;
use Magento\View\Design\ThemeInterface;

/**
 * Factory that produces layout file instances
 */
class Factory
{
    /**
     * Object manager
     *
     * @var ObjectManager
     */
    private $objectManager;

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
     * Return newly created instance of a layout file
     *
     * @param string $filename
     * @param string $module
     * @param ThemeInterface $theme
     * @return \Magento\View\Layout\File
     */
    public function create($filename, $module, ThemeInterface $theme = null)
    {
        return $this->objectManager->create(
            'Magento\View\Layout\File',
            array(
                'filename' => $filename,
                'module' => $module,
                'theme' => $theme,
            )
        );
    }
}
