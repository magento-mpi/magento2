<?php
/**
 * {license_notice}
 *   
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Catalog\Model\Indexer\Category\Flat\Plugin;

class IndexerConfigData
{
    /**
     * @var \Magento\Catalog\Model\Indexer\Category\Flat\State
     */
    protected $state;

    /**
     * @param \Magento\Catalog\Model\Indexer\Category\Flat\State $state
     */
    public function __construct(\Magento\Catalog\Model\Indexer\Category\Flat\State $state)
    {
        $this->state = $state;
    }

    /**
     * Unset indexer data in configuration if flat is disabled
     *
     * @param array $arguments
     * @param \Magento\Code\Plugin\InvocationChain $invocationChain
     * @return mixed
     */
    public function aroundGet(array $arguments, \Magento\Code\Plugin\InvocationChain $invocationChain)
    {
        $data = $invocationChain->proceed($arguments);

        if (!$this->state->isFlatEnabled()) {
            $indexerId = \Magento\Catalog\Model\Indexer\Category\Flat\State::INDEXER_ID;
            if ((!isset($arguments[0]) || !$arguments[0]) && isset($data[$indexerId])) {
                unset($data[$indexerId]);
            } elseif (isset($arguments[0])) {
                list($firstKey, ) = explode('/', $arguments[0]);
                if ($firstKey == $indexerId) {
                    $data = isset($arguments[1]) ? $arguments[1] : null;
                }
            }
        }

        return $data;
    }
}
