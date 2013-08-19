<?php
/**
 * {license_notice}
 *
 * @copyright {copyright}
 * @license   {license_link}
 */
class Mage_Core_Model_Module_Declaration_FileResolver implements Magento_Config_FileResolverInterface
{
    /**
     * @var Mage_Core_Model_Dir
     */
    protected $_applicationDirs;

    /**
     * @param Mage_Core_Model_Dir $applicationDirs
     */
    public function __construct(Mage_Core_Model_Dir $applicationDirs)
    {
        $this->_applicationDirs = $applicationDirs;
    }

    /**
     * {@inheritdoc}
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    public function get($filename, $scope)
    {
        $appCodeDir =  $this->_applicationDirs->getDir(Mage_Core_Model_Dir::MODULES);
        $moduleFilePattern = $appCodeDir . DIRECTORY_SEPARATOR . '*' . DIRECTORY_SEPARATOR . '*' . DIRECTORY_SEPARATOR
            . 'etc' . DIRECTORY_SEPARATOR . 'module.xml';
        $moduleFileList = glob($moduleFilePattern);

        $mageScopePath = $appCodeDir . DIRECTORY_SEPARATOR . 'Mage' . DIRECTORY_SEPARATOR;
        $output = array(
            'base' => array(),
            'mage' => array(),
            'custom' => array(),
        );
        foreach ($moduleFileList as $file) {
            $scope = strpos($file, $mageScopePath) === 0 ? 'mage' : 'custom';
            $output[$scope][] = $file;
        }

        $appConfigDir = $this->_applicationDirs->getDir(Mage_Core_Model_Dir::CONFIG);
        $globalEnablerPattern = $appConfigDir . DIRECTORY_SEPARATOR . '*' . DIRECTORY_SEPARATOR . 'module.xml';
        $output['base'] = glob($globalEnablerPattern);
        // Put global enablers at the end of the file list
        return array_merge($output['mage'], $output['custom'], $output['base']);
    }

}
