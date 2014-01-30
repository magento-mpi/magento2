<?php
/**
 * {license_notice}
 *   
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Catalog\Model\Indexer\Category\Flat\Plugin;

class SystemConfig
{
    /**
     * @var \Magento\Indexer\Model\Indexer
     */
    protected $indexer;

    /**
     * @var bool
     */
    protected $flatFlag;

    /**
     * @param \Magento\Indexer\Model\Indexer $indexer
     * @param \Magento\Catalog\Model\Indexer\Category\Flat\Config $config
     */
    public function __construct(
        \Magento\Indexer\Model\Indexer $indexer,
        \Magento\Catalog\Model\Indexer\Category\Flat\Config $config
    ) {
        $this->indexer = $indexer;
        $this->flatFlag = $config->isFlatEnabled();
    }

    /**
     * Return own indexer object
     *
     * @return \Magento\Indexer\Model\Indexer
     */
    protected function getIndexer()
    {
        if (!$this->indexer->getId()) {
            $this->indexer->load(\Magento\Catalog\Model\Indexer\Category\Flat\Config::INDEXER_ID);
        }
        return $this->indexer;
    }

    /**
     * @param array $arguments
     * @param \Magento\Code\Plugin\InvocationChain $invocationChain
     * @return mixed
     */
    public function aroundSave(array $arguments, \Magento\Code\Plugin\InvocationChain $invocationChain)
    {
        /** @var \Magento\Core\Model\Config\Value $config */
        $config = $arguments[0];
        $path   = $config->getPath();
        $value  = $config->getValue();
        $objectResource = $invocationChain->proceed($arguments);
        if ($path == \Magento\Catalog\Model\Indexer\Category\Flat\Config::XML_PATH_IS_ENABLED_FLAT_CATALOG_CATEGORY
            && (bool)$value === true && $this->flatFlag === false
        ) {
            $this->getIndexer()->getState()
                ->setStatus(\Magento\Indexer\Model\Indexer\State::STATUS_INVALID)
                ->save();
        }

        return $objectResource;
    }
}
