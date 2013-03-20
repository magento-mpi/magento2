<?php
/**
 * Saas edition configurator
 *
 * {license_notice}
 *
 * @copyright  {copyright}
 * @license    {license_link}
 */
class SaasConfigurator implements ConfiguratorInterface
{
    /**
     * Base instance path
     *
     * @var string
     */
    protected $_basePath;

    /**
     * @var Varien_Io_File
     */
    protected $_filesystem;

    /**
     * @param $basePath
     * @param Varien_Io_File $filesystem
     */
    public function __construct($basePath, Varien_Io_File $filesystem)
    {
        $this->_basePath = $basePath;
        $this->_filesystem = $filesystem;
    }

    /**
     * Configure Magento instance
     *
     * @return void
     */
    public function configure()
    {
        $enablerPath = $this->_basePath
            . DIRECTORY_SEPARATOR . 'app'
            . DIRECTORY_SEPARATOR . 'etc'
            . DIRECTORY_SEPARATOR . 'modules'
            . DIRECTORY_SEPARATOR;

        //enable saas edition modules
        $this->_filesystem->cp($enablerPath . 'XSaas_Edition.xml.dist', $enablerPath . 'XSaas_Edition.xml');
    }
}