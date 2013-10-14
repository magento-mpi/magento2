<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Factory that produces layout file instances
 */
namespace Magento\View\Layout\File;

use Magento\ObjectManager;
use Magento\View\Design\Theme;

class Factory
{
    /**
     * @var ObjectManager
     */
    private $_objectManager;

    /**
     * @param ObjectManager $objectManager
     */
    public function __construct(ObjectManager $objectManager)
    {
        $this->_objectManager = $objectManager;
    }

    /**
     * Return newly created instance of a layout file
     *
     * @param string $filename
     * @param string $module
     * @param Theme $theme
     * @return \Magento\View\Layout\File
     */
    public function create($filename, $module, Theme $theme = null)
    {
        return $this->_objectManager->create(
            'Magento\View\Layout\File',
            array('filename' => $filename, 'module' => $module, 'theme' => $theme)
        );
    }
}
