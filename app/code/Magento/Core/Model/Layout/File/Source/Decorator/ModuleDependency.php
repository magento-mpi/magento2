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
class Magento_Core_Model_Layout_File_Source_Decorator_ModuleDependency
    implements Magento_Core_Model_Layout_File_SourceInterface
{
    /**
     * @var Magento_Core_Model_Layout_File_SourceInterface
     */
    private $_subject;

    /**
     * @var Magento_Core_Model_ModuleListInterface
     */
    private $_moduleList;

    /**
     * Fully-qualified names of modules, ordered by their priority in the system
     *
     * @var array|null
     */
    private $_orderedModules = null;

    /**
     * @param Magento_Core_Model_Layout_File_SourceInterface $subject
     * @param Magento_Core_Model_ModuleListInterface $listInterface
     */
    public function __construct(
        Magento_Core_Model_Layout_File_SourceInterface $subject,
        Magento_Core_Model_ModuleListInterface $listInterface
    ) {
        $this->_subject = $subject;
        $this->_moduleList = $listInterface;
    }

    /**
     * Retrieve layout files, sorted by the priority of modules they belong to
     *
     * {@inheritdoc}
     */
    public function getFiles(Magento_Core_Model_ThemeInterface $theme)
    {
        $result = $this->_subject->getFiles($theme);
        usort($result, array($this, 'compareFiles'));
        return $result;
    }

    /**
     * Compare layout files according to the priority of modules they belong to. To be used as a callback for sorting.
     *
     * @param Magento_Core_Model_Layout_File $fileOne
     * @param Magento_Core_Model_Layout_File $fileTwo
     * @return int
     */
    public function compareFiles(Magento_Core_Model_Layout_File $fileOne, Magento_Core_Model_Layout_File $fileTwo)
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
