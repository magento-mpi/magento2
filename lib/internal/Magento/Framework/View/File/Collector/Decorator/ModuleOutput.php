<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Framework\View\File\Collector\Decorator;

use Magento\Framework\View\File\CollectorInterface;
use Magento\Framework\View\File;
use Magento\Framework\Module\Manager;
use Magento\Framework\View\Design\ThemeInterface;

/**
 * Decorator that filters out view files that belong to modules, output of which is prohibited
 */
class ModuleOutput implements CollectorInterface
{
    /**
     * Subject
     *
     * @var CollectorInterface
     */
    private $subject;

    /**
     * Module manager
     *
     * @var \Magento\Framework\Module\Manager
     */
    private $moduleManager;

    /**
     * Constructor
     *
     * @param CollectorInterface $subject
     * @param Manager $moduleManager
     */
    public function __construct(
        CollectorInterface $subject,
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
     * @return array|\Magento\Framework\View\File[]
     */
    public function getFiles(ThemeInterface $theme, $filePath)
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
