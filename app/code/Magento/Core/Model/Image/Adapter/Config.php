<?php
/**
 * {license_notice}
 *   
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Core\Model\Image\Adapter;

class Config implements \Magento\Image\Adapter\ConfigInterface
{
    const XML_PATH_IMAGE_ADAPTER = 'dev/image/default_adapter';
    const XML_PATH_IMAGE_ADAPTERS = 'dev/image/adapters';

    /**
     * @var \Magento\Core\Model\ConfigInterface
     */
    protected $config;

    /**
     * @param \Magento\Core\Model\ConfigInterface $config
     */
    public function __construct(\Magento\Core\Model\ConfigInterface $config)
    {
        $this->config = $config;
    }

    /**
     * {@inherit}
     */
    public function getAdapterAlias()
    {
        return (string)$this->config->getValue(self::XML_PATH_IMAGE_ADAPTER);
    }

    /**
     * {@inherit}
     */
    public function getAdapters()
    {
        return $this->config->getValue(self::XML_PATH_IMAGE_ADAPTERS);
    }
}
