<?php
/**
 * Module Output Config Model
 *
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Module\Output;

class Config implements \Magento\Module\Output\ConfigInterface
{
    /**
     * XPath in the configuration where module statuses are stored
     */
    const XML_PATH_MODULE_OUTPUT_STATUS = 'advanced/modules_disable_output/%s';

    /**
     * @var \Magento\App\Config\ScopeConfigInterface
     */
    protected $_scopeConfig;

    /**
     * @var string
     */
    protected $_storeType;

    /**
     * @param \Magento\App\Config\ScopeConfigInterface $scopeConfig
     * @param string $scopeType
     */
    public function __construct(
        \Magento\App\Config\ScopeConfigInterface $scopeConfig,
        $scopeType
    ) {
        $this->_scopeConfig = $scopeConfig;
        $this->_storeType = $scopeType;
    }

    /**
     * @inheritdoc
     */
    public function isEnabled($moduleName)
    {
        return $this->isSetFlag(sprintf(self::XML_PATH_MODULE_OUTPUT_STATUS, $moduleName));
    }

    /**
     * @inheritdoc
     */
    public function isSetFlag($path)
    {
        return $this->_scopeConfig->isSetFlag($path, $this->_storeType);
    }
}
