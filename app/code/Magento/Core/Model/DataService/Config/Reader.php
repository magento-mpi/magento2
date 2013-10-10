<?php
/**
 * Magento Data Service Config reader
 *
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Core\Model\DataService\Config;

class Reader extends \Magento\Config\AbstractXml
{
    /**
     * @var \Magento\Core\Model\Config\Modules\Reader
     */
    private $_modulesReader;

    /**
     * @param \Magento\Core\Model\Config\Modules\Reader $moduleReader
     * @param array $configFiles
     */
    public function __construct(
        \Magento\Core\Model\Config\Modules\Reader $moduleReader,
        array $configFiles
    ) {
        if (count($configFiles)) {
            parent::__construct($configFiles);
        }
        $this->_modulesReader = $moduleReader;
    }

    /**
     * Get absolute path to the XML-schema file
     *
     * @return string
     */
    public function getSchemaFile()
    {
        return $this->_modulesReader->getModuleDir('etc', 'Magento_Core') . '/service_calls.xsd';
    }

    /**
     * Extract configuration data from the DOM structure
     *
     * @param \DOMDocument $dom
     * @return array
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    protected function _extractData(\DOMDocument $dom)
    {
        return array();
    }

    /**
     * Get XML-contents, initial for merging
     *
     * @return string
     */
    protected function _getInitialXml()
    {
        return '<?xml version="1.0"?><service_calls></service_calls>';
    }

    /**
     * Get if xml files must be runtime validated
     *
     * @return boolean
     */
    protected function _isRuntimeValidated()
    {
        return false;
    }

    /**
     * Retrieve Service Calls
     *
     * @return \DOMDocument
     */
    public function getServiceCallConfig()
    {
        return $this->_getDomConfigModel()->getDom();
    }

    /**
     * Get list of paths to identifiable nodes
     *
     * @return array
     */
    protected function _getIdAttributes()
    {
        return array(
            '/service_calls/service_call/arg' => 'name',
            '/service_calls/service_call' => 'name',
        );
    }

    /**
     * Perform xml validation
     *
     * @return \Magento\Config\AbstractXml
     * @throws \Magento\Exception if invalid XML-file passed
     */
    public function validate()
    {
        return $this->_performValidate();
    }
}
