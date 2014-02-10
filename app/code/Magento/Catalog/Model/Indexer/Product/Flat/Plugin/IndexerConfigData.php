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
     * @var \Magento\Catalog\Helper\Product\Flat
     */
    protected $_helper;

    /**
     * @param \Magento\Catalog\Helper\Product\Flat $helper
     */
    public function __construct(
        \Magento\Catalog\Helper\Product\Flat $helper
    ) {
        $this->_helper = $helper;
    }

    /**
     * @param array $arguments
     * @param \Magento\Code\Plugin\InvocationChain $invocationChain
     * @return mixed|null
     */
    public function aroundGet(array $arguments, \Magento\Code\Plugin\InvocationChain $invocationChain)
    {
        $data = $invocationChain->proceed($arguments);

        if (!$this->_helper->isEnabled()) {
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
