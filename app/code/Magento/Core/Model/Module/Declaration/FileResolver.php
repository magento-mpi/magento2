<?php
/**
 * {license_notice}
 *
 * @copyright {copyright}
 * @license   {license_link}
 */
namespace Magento\Core\Model\Module\Declaration;

class FileResolver implements \Magento\Config\FileResolverInterface
{
    /**
     * @var \Magento\Core\Model\Dir
     */
    protected $_applicationDirs;

    /**
     * @param \Magento\Core\Model\Dir $applicationDirs
     */
    public function __construct(\Magento\Core\Model\Dir $applicationDirs)
    {
        $this->_applicationDirs = $applicationDirs;
    }

    /**
     * {@inheritdoc}
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    public function get($filename, $scope)
    {
        $appCodeDir =  $this->_applicationDirs->getDir(\Magento\Core\Model\Dir::MODULES);
        $moduleFilePattern = $appCodeDir . DIRECTORY_SEPARATOR . '*' . DIRECTORY_SEPARATOR . '*' . DIRECTORY_SEPARATOR
            . 'etc' . DIRECTORY_SEPARATOR . 'module.xml';
        $moduleFileList = glob($moduleFilePattern);

        $mageScopePath = $appCodeDir . DIRECTORY_SEPARATOR . 'Magento' . DIRECTORY_SEPARATOR;
        $output = array(
            'base' => array(),
            'mage' => array(),
            'custom' => array(),
        );
        foreach ($moduleFileList as $file) {
            $scope = strpos($file, $mageScopePath) === 0 ? 'mage' : 'custom';
            $output[$scope][] = $file;
        }

        $appConfigDir = $this->_applicationDirs->getDir(\Magento\Core\Model\Dir::CONFIG);
        $globalEnablerPattern = $appConfigDir . DIRECTORY_SEPARATOR . '*' . DIRECTORY_SEPARATOR . 'module.xml';
        $output['base'] = glob($globalEnablerPattern);
        // Put global enablers at the end of the file list
        return array_merge($output['mage'], $output['custom'], $output['base']);
    }

}
