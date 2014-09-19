<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Rss\Model;

use \Magento\Framework\App\Rss\DataProviderInterface;
use \Magento\Framework\App\Rss\RssManagerInterface;

/**
 * Rss Manager
 *
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class RssManager implements RssManagerInterface
{
    /**
     * @var \Magento\Framework\App\Rss\DataProviderInterface[]
     */
    protected $providers;

    /**
     * @param \Magento\Framework\ObjectManager $objectManager
     * @param array $dataProviders
     */
    public function __construct(\Magento\Framework\ObjectManager $objectManager, array $dataProviders = array())
    {
        $this->objectManager = $objectManager;
        $this->providers = $dataProviders;
    }

    /**
     * Return Rss Data Provider by Rss Feed Id.
     *
     * @param string $type
     * @return DataProviderInterface
     * @throws \InvalidArgumentException
     */
    public function getProvider($type)
    {
        if (!isset($this->providers[$type])) {
            throw new \InvalidArgumentException('Unknown provider with type: ' . $type);
        }

        $provider = $this->providers[$type];

        if (is_string($provider)) {
            $provider = $this->objectManager->get($provider);
        }

        if (!$provider instanceof DataProviderInterface) {
            throw new \InvalidArgumentException('Provider should implement DataProviderInterface');
        }

        $this->providers[$type] = $provider;

        return $this->providers[$type];
    }

    /**
     * {@inheritdoc}
     */
    public function getProviders()
    {
        $result = array();
        foreach (array_keys($this->providers) as $type) {
            $result[] = $this->getProvider($type);
        }
        return $result;
    }
}
