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
namespace Magento\Core\Model\Layout\File;

class Factory
{
    /**
     * @var \Magento\ObjectManager
     */
    private $_objectManager;

    /**
     * @param \Magento\ObjectManager $objectManager
     */
    public function __construct(\Magento\ObjectManager $objectManager)
    {
        $this->_objectManager = $objectManager;
    }

    /**
     * Return newly created instance of a layout file
     *
     * @param string $filename
     * @param string $module
     * @param \Magento\View\Design\ThemeInterface $theme
     * @return \Magento\Core\Model\Layout\File
     */
    public function create($filename, $module, \Magento\View\Design\ThemeInterface $theme = null)
    {
        return $this->_objectManager->create(
            'Magento\Core\Model\Layout\File',
            array('filename' => $filename, 'module' => $module, 'theme' => $theme)
        );
    }
}
