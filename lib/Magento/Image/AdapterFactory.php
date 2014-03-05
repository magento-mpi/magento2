<?php
/**
 * {license_notice}
 *
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
     * @param string $adapterAlias
     * @return \Magento\Image\Adapter\AdapterInterface
     * @throws \InvalidArgumentException
     */
    public function create($adapterAlias = null)
    {
        $adapterAlias = !empty($adapterAlias) ? $adapterAlias : $this->config->getAdapterAlias();
        if (empty($adapterAlias)) {
            throw new \InvalidArgumentException('Image adapter is not selected.');
        }
        if (empty($this->adapterMap[$adapterAlias]['class'])) {
            throw new \InvalidArgumentException("Image adapter for '{$adapterAlias}' is not setup.");
        }
        $imageAdapter = $this->objectManager->create($this->adapterMap[$adapterAlias]['class']);
        if (!$imageAdapter instanceof Adapter\AdapterInterface) {
            throw new \InvalidArgumentException(
                $this->adapterMap[$adapterAlias]['class']
                . ' is not instance of \Magento\Image\Adapter\AdapterInterface'
            );
        }
        $imageAdapter->checkDependencies();
        return $imageAdapter;
    }
}
