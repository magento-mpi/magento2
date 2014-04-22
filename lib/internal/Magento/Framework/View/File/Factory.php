<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Framework\View\File;

use Magento\ObjectManager;
use Magento\Framework\View\Design\ThemeInterface;

/**
 * Factory that produces view file instances
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
     * Return newly created instance of a view file
     *
     * @param string $filename
     * @param string $module
     * @param ThemeInterface|null $theme
     * @return \Magento\Framework\View\File
     */
    public function create($filename, $module = '', ThemeInterface $theme = null)
    {
        return $this->objectManager->create(
            'Magento\Framework\View\File',
            array(
                'filename' => $filename,
                'module' => $module,
                'theme' => $theme,
            )
        );
    }
}
