<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Image\Adapter;

class Config implements \Magento\Image\Adapter\ConfigInterface
{
    const XML_PATH_IMAGE_ADAPTER = 'dev/image/default_adapter';
    const XML_PATH_IMAGE_ADAPTERS = 'dev/image/adapters';

    /**
     * @var \Magento\App\ConfigInterface
     */
    protected $config;

    /**
     * @param \Magento\App\ConfigInterface $config
     */
    public function __construct(\Magento\App\ConfigInterface $config)
    {
        $this->config = $config;
    }

    /**
     * {@inherit}
     *
     * @return string
     */
    public function getAdapterAlias()
    {
        return (string)$this->config->getValue(self::XML_PATH_IMAGE_ADAPTER);
    }

    /**
     * {@inherit}
     *
     * @return mixed
     */
    public function getAdapters()
    {
        return $this->config->getValue(self::XML_PATH_IMAGE_ADAPTERS);
    }
}
