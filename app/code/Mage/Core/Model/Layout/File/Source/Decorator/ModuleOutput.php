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
class Mage_Core_Model_Layout_File_Source_Decorator_ModuleOutput implements Mage_Core_Model_Layout_File_SourceInterface
{
    /**
     * @var Mage_Core_Model_Layout_File_SourceInterface
     */
    private $_subject;

    /**
     * @var Mage_Core_Model_ModuleManager
     */
    private $_moduleManager;

    /**
     * @param Mage_Core_Model_Layout_File_SourceInterface $subject
     * @param Mage_Core_Model_ModuleManager $moduleManager
     */
    public function __construct(
        Mage_Core_Model_Layout_File_SourceInterface $subject,
        Mage_Core_Model_ModuleManager $moduleManager
    ) {
        $this->_subject = $subject;
        $this->_moduleManager = $moduleManager;
    }

    /**
     * Filter out theme files that belong to inactive modules or ones explicitly configured to not produce any output
     *
     * {@inheritdoc}
     */
    public function getFiles(Mage_Core_Model_ThemeInterface $theme)
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
