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
     * @var array
     */
    protected $adapterMap;

    /**
     * @param \Magento\ObjectManager $objectManager
     * @param Adapter\ConfigInterface $config
     * @param array $adapterMap
     */
    public function __construct(
        \Magento\ObjectManager $objectManager,
        \Magento\Image\Adapter\ConfigInterface $config,
        array $adapterMap = array()
    ) {
        $this->objectManager = $objectManager;
        $this->config = $config;
        $this->adapterMap = array_merge($config->getAdapters(), $adapterMap);
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
        if (empty($this->adapterMap[$adapterName]['class'])) {
            throw new \InvalidArgumentException('Image adapter is not setup.');
        }
        $imageAdapter = $this->objectManager->create($this->adapterMap[$adapterName]['class']);
        if (!$imageAdapter instanceof Adapter\AdapterInterface) {
            throw new \InvalidArgumentException(
                $this->adapterMap[$adapterName]['class'] . ' is not instance of \Magento\Image\Adapter\AdapterInterface'
            );
        }
        $imageAdapter->checkDependencies();
        return $imageAdapter;
    }
}
