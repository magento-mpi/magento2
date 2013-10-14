<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Decorator that sorts layout files according to dependencies between modules they belong to
 */
namespace Magento\View\Layout\File\Source\Decorator;

use Magento\View\Layout\File\Source;
use Magento\View\Layout\File;
use Magento\Core\Model\ModuleListInterface;
use Magento\View\Design\Theme;

class ModuleDependency implements Source
{
    /**
     * @var Source
     */
    private $_subject;

    /**
     * @var ModuleListInterface
     */
    private $_moduleList;

    /**
     * Fully-qualified names of modules, ordered by their priority in the system
     *
     * @var array|null
     */
    private $_orderedModules = null;

    /**
     * @param Source $subject
     * @param ModuleListInterface $listInterface
     */
    public function __construct(
        Source $subject,
        ModuleListInterface $listInterface
    ) {
        $this->_subject = $subject;
        $this->_moduleList = $listInterface;
    }

    /**
     * Retrieve layout files, sorted by the priority of modules they belong to
     *
     * {@inheritdoc}
     */
    public function getFiles(Theme $theme, $filePath = '*')
    {
        $result = $this->_subject->getFiles($theme, $filePath);
        usort($result, array($this, 'compareFiles'));
        return $result;
    }

    /**
     * Compare layout files according to the priority of modules they belong to. To be used as a callback for sorting.
     *
     * @param File $fileOne
     * @param File $fileTwo
     * @return int
     */
    public function compareFiles(File $fileOne, File $fileTwo)
    {
        if ($fileOne->getModule() == $fileTwo->getModule()) {
            return strcmp($fileOne->getName(), $fileTwo->getName());
        }
        $moduleOnePriority = $this->_getModulePriority($fileOne->getModule());
        $moduleTwoPriority = $this->_getModulePriority($fileTwo->getModule());
        if ($moduleOnePriority == $moduleTwoPriority) {
            return strcmp($fileOne->getModule(), $fileTwo->getModule());
        }
        return ($moduleOnePriority < $moduleTwoPriority ? -1 : 1);
    }

    /**
     * Retrieve priority of a module relatively to other modules in the system
     *
     * @param string $moduleName
     * @return int
     */
    protected function _getModulePriority($moduleName)
    {
        if ($this->_orderedModules === null) {
            $this->_orderedModules = array();
            foreach ($this->_moduleList->getModules() as $module) {
                $this->_orderedModules[] = $module['name'];
            }
        }
        $result = array_search($moduleName, $this->_orderedModules);
        // assume unknown modules have the same priority, distinctive from known modules
        if ($result === false) {
            return -1;
        }
        return $result;
    }
}
