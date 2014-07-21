<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\UrlRedirect\Model;

class GeneratorFactory
{
    const XML_PATH_PREFIX = 'url_rewrite/entity_types/';

    /**
     * @var \Magento\Framework\ObjectManager
     */
    protected $objectManager;

    /**
     * @var \Magento\Framework\App\Config\ScopeConfigInterface
     */
    protected $config;

    /**
     * @param \Magento\Framework\ObjectManager $objectManager
     * @param \Magento\Framework\App\Config\ScopeConfigInterface $config
     */
    public function __construct(
        \Magento\Framework\ObjectManager $objectManager,
        \Magento\Framework\App\Config\ScopeConfigInterface $config
    ) {
        $this->objectManager = $objectManager;
        $this->config = $config;
    }

    /**
     * Get generator by specified entity type
     *
     * @param string $entityType
     * @return \Magento\Framework\Object
     */
    public function get($entityType)
    {
        return $this->objectManager->get(
            (string)$this->config->getValue(self::XML_PATH_PREFIX . $entityType . '/generator')
        )->setType($entityType);
    }
}
