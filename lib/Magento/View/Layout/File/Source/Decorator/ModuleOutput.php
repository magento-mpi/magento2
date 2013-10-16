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
use Magento\View\Design\Theme;

class ModuleOutput implements Source
{
    /**
     * @var Source
     */
    private $_subject;

    /**
     * @var \Magento\Core\Model\ModuleManager
     */
    private $_moduleManager;

    /**
     * @param Source $subject
     * @param ModuleManager $moduleManager
     */
    public function __construct(
        Source $subject,
        ModuleManager $moduleManager
    ) {
        $this->_subject = $subject;
        $this->_moduleManager = $moduleManager;
    }

    /**
     * Filter out theme files that belong to inactive modules or ones explicitly configured to not produce any output
     *
     * {@inheritdoc}
     */
    public function getFiles(Theme $theme, $filePath = '*')
    {
        $result = array();
        foreach ($this->_subject->getFiles($theme, $filePath) as $file) {
            if ($this->_moduleManager->isOutputEnabled($file->getModule())) {
                $result[] = $file;
            }
        }
        return $result;
    }
}
