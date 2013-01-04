<?php
/**
 * API ACL Config Reader model.
 *
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
class Mage_Webapi_Model_Authorization_Config_Reader extends Magento_Acl_Config_Reader
{
    /**
     * @var Mage_Core_Model_Config
     */
    protected $_config;

    /**
     * @param Mage_Core_Model_Config $config
     * @param array $configFiles
     * @throws InvalidArgumentException
     */
    public function __construct(
        Mage_Core_Model_Config $config,
        array $configFiles
    ) {
        parent::__construct($configFiles);
        $this->_config = $config;
    }

    /**
     * Get absolute path to the XML-schema file.
     *
     * @return string
     */
    public function getSchemaFile()
    {
        return $this->_config->getModuleDir('etc', 'Mage_Webapi') . DIRECTORY_SEPARATOR . 'acl.xsd';
    }

    /**
     * Get XML-contents, initial for merging.
     *
     * @return string
     */
    protected function _getInitialXml()
    {
        return '<?xml version="1.0" encoding="utf-8"?><config><acl></acl><mapping></mapping></config>';
    }
}
