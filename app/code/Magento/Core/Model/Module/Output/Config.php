<?php
/**
 * Module Output Config Model
 *
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Core\Model\Module\Output;


class Config implements \Magento\Module\Output\ConfigInterface
{
    /**
     * XPath in the configuration where module statuses are stored
     */
    const XML_PATH_MODULE_OUTPUT_STATUS = 'advanced/modules_disable_output/%s';

    /**
     * @var \Magento\Core\Model\Store\ConfigInterface
     */
    protected $_storeConfig;

    /**
     * @param \Magento\Core\Model\Store\ConfigInterface $storeConfig
     */
    public function __construct(\Magento\Core\Model\Store\ConfigInterface $storeConfig)
    {
        $this->_storeConfig =  $storeConfig;
    }

    /**
     * @inheritdoc
     */
    public function isEnabled($moduleName)
    {
        return $this->getFlag(sprintf(self::XML_PATH_MODULE_OUTPUT_STATUS, $moduleName));
    }

    /**
     * @inheritdoc
     */
    public function getFlag($path)
    {
        return $this->_storeConfig->getConfigFlag($path);
    }
}
