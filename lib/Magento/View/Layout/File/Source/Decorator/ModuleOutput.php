<?php
/**
 * Decorator that filters out layout files that belong to modules, output of which is prohibited
 *
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\View\Layout\File\Source\Decorator;

use Magento\View\Layout\File\Source;
use Magento\View\Layout\File;
use Magento\Core\Model\ModuleManager;
use Magento\View\Design\ThemeInterface;

class ModuleOutput implements Source
{
    /**
     * @var Source
     */
    private $subject;

    /**
     * @var \Magento\Core\Model\ModuleManager
     */
    private $moduleManager;

    /**
     * @param Source $subject
     * @param ModuleManager $moduleManager
     */
    public function __construct(
        Source $subject,
        ModuleManager $moduleManager
    ) {
        $this->subject = $subject;
        $this->moduleManager = $moduleManager;
    }

    /**
     * Filter out theme files that belong to inactive modules or ones explicitly configured to not produce any output
     *
     * {@inheritdoc}
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
