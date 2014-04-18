<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Framework\View\Layout\File;

use Magento\ObjectManager;
use Magento\Framework\View\Design\ThemeInterface;

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
     * @param ThemeInterface|null $theme
     * @return \Magento\Framework\View\Layout\File
     */
    public function create($filename, $module, ThemeInterface $theme = null)
    {
        return $this->objectManager->create(
            'Magento\Framework\View\Layout\File',
            array('filename' => $filename, 'module' => $module, 'theme' => $theme)
        );
    }
}
