<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\View\File\Source\Decorator;

use Magento\View\File\SourceInterface;
use Magento\View\File;
use Magento\Module\Manager;
use Magento\View\Design\ThemeInterface;

/**
 * Decorator that filters out view files that belong to modules, output of which is prohibited
 */
class ModuleOutput implements SourceInterface
{
    /**
     * Subject
     *
     * @var SourceInterface
     */
    private $subject;

    /**
     * Module manager
     *
     * @var \Magento\Module\Manager
     */
    private $moduleManager;

    /**
     * Constructor
     *
     * @param SourceInterface $subject
     * @param Manager $moduleManager
     */
    public function __construct(
        SourceInterface $subject,
        Manager $moduleManager
    ) {
        $this->subject = $subject;
        $this->moduleManager = $moduleManager;
    }

    /**
     * Retrieve files
     *
     * Filter out theme files that belong to inactive modules or ones explicitly configured to not produce any output
     *
     * @param ThemeInterface $theme
     * @param string $filePath
     * @return array|\Magento\View\File[]
     */
    public function getFiles(ThemeInterface $theme, $filePath = '*')
    {
        $result = array();
        foreach ($this->subject->getFiles($theme, $filePath) as $file) {
            if ($this->moduleManager->isOutputEnabled($file->getModule())) {
                $result[] = $file;
            }
        }
        return $result;
    }
}
