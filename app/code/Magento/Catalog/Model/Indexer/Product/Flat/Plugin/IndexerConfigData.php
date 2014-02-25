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

        if (!$this->_helper->isEnabled()) {
            $indexerId = \Magento\Catalog\Model\Indexer\Product\Flat\Processor::INDEXER_ID;
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
