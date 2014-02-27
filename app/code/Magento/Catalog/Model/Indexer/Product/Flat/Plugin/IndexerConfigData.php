<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Catalog
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Catalog\Model\Indexer\Product\Flat\Plugin;

class IndexerConfigData
{
    /**
     * @var \Magento\Catalog\Model\Indexer\Product\Flat\State
     */
    protected $_state;

    /**
     * @param \Magento\Catalog\Model\Indexer\Product\Flat\State $state
     */
    public function __construct(
        \Magento\Catalog\Model\Indexer\Product\Flat\State $state
    ) {
        $this->_state = $state;
    }

    /**
     * Around get handler
     *
     * @param array $arguments
     * @param \Magento\Code\Plugin\InvocationChain $invocationChain
     *
     * @return mixed|null
     */
    public function aroundGet(array $arguments, \Magento\Code\Plugin\InvocationChain $invocationChain)
    {
        $data = $invocationChain->proceed($arguments);

        if (!$this->_state->isFlatEnabled()) {
            $indexerId = \Magento\Catalog\Model\Indexer\Product\Flat\Processor::INDEXER_ID;
            if ((!isset($arguments['path']) || !$arguments['path']) && isset($data[$indexerId])) {
                unset($data[$indexerId]);
            } elseif (isset($arguments['path'])) {
                list($firstKey, ) = explode('/', $arguments['path']);
                if ($firstKey == $indexerId) {
                    $data = isset($arguments['default']) ? $arguments['default'] : null;
                }
            }
        }

        return $data;
    }
}
