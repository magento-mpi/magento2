<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Decorator that filters out layout files that belong to modules, output of which is prohibited
 */
namespace Magento\Core\Model\Layout\File\Source\Decorator;

class ModuleOutput implements \Magento\View\Layout\File\SourceInterface
{
    /**
     * @var \Magento\View\Layout\File\SourceInterface
     */
    private $_subject;

    /**
     * @var \Magento\Core\Model\ModuleManager
     */
    private $_moduleManager;

    /**
     * @param \Magento\View\Layout\File\SourceInterface $subject
     * @param \Magento\Core\Model\ModuleManager $moduleManager
     */
    public function __construct(
        \Magento\View\Layout\File\SourceInterface $subject,
        \Magento\Core\Model\ModuleManager $moduleManager
    ) {
        $this->_subject = $subject;
        $this->_moduleManager = $moduleManager;
    }

    /**
     * Filter out theme files that belong to inactive modules or ones explicitly configured to not produce any output
     *
     * {@inheritdoc}
     */
    public function getFiles(\Magento\View\Design\ThemeInterface $theme)
    {
        $result = array();
        foreach ($this->_subject->getFiles($theme) as $file) {
            if ($this->_moduleManager->isOutputEnabled($file->getModule())) {
                $result[] = $file;
            }
        }
        return $result;
    }
}
