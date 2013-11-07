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
    const XML_PATH_IMAGE_ADAPTER = 'default/dev/image/adapter';

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
    public function getAdapterName()
    {
        return (string)$this->config->getValue(self::XML_PATH_IMAGE_ADAPTER);
    }
}