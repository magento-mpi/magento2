<?php
/**
 * Media downloader entry point
 *
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
class Mage_Core_Model_EntryPoint_Media extends Mage_Core_Model_EntryPointAbstract
{
    /**
     * @param string $baseDir
     * @param array $mediaDirectory
     * @param array $params
     * @param string $objectManagerClass
     */
    public function __construct(
        $baseDir, $mediaDirectory, array $params = array(), $objectManagerClass = 'Mage_Core_Model_ObjectManager_Http'
    ) {
        if (empty($mediaDirectory)) {
            $params[Mage::PARAM_ALLOWED_MODULES] = array('Mage_Core');
            $params[Mage::PARAM_CACHE_OPTIONS]['disable_save'] = true;
        }
        parent::__construct($baseDir, $params, $objectManagerClass);
    }

    /**
     * Process request to application
     */
    public function processRequest()
    {
        // TODO: Move configuration file generation and media files materialization here from get.php
    }
}
