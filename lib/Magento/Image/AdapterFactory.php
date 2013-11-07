<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Core
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Image;

class AdapterFactory
{
    /**
     * @var Adapter\ConfigInterface
     */
    protected $config;

    /**
     * @var \Magento\ObjectManager
     */
    protected $objectManager;

    /**
     * @param \Magento\ObjectManager $objectManager
     * @param Adapter\ConfigInterface $config
     */
    public function __construct(
        \Magento\ObjectManager $objectManager,
        \Magento\Image\Adapter\ConfigInterface $config
    ) {
        $this->objectManager = $objectManager;
        $this->config = $config;
    }

    /**
     * Return specified image adapter
     *
     * @param string $adapterName
     * @return \Magento\Image\Adapter\AdapterInterface
     * @throws \InvalidArgumentException
     */
    public function create($adapterName = null)
    {
        $adapterName = !empty($adapterName) ? $adapterName : $this->config->getAdapterName();
        if (empty($adapterName)) {
            throw new \InvalidArgumentException('Image adapter is not selected.');
        }
        $imageAdapter = $this->objectManager->create($adapterName);
        if (!$imageAdapter instanceof Adapter\AdapterInterface) {
            throw new \InvalidArgumentException(
                $adapterName . ' is not instance of \Magento\Image\Adapter\AdapterInterface'
            );
        }
        $imageAdapter->checkDependencies();
        return $imageAdapter;
    }
}
