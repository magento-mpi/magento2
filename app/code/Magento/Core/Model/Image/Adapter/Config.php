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
    protected $configModel;

    /**
     * @param \Magento\Core\Model\ConfigInterface $configModel
     */
    public function __construct(\Magento\Core\Model\ConfigInterface $configModel)
    {
        $this->configModel = $configModel;
    }

    /**
     * {@inherit}
     */
    public function getAdapterName()
    {
        return (string)$this->configModel->getNode(self::XML_PATH_IMAGE_ADAPTER);
    }
}