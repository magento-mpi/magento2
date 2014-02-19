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
     * @param \Magento\Indexer\Model\Config\Data $subject
     * @param callable $proceed
     * @param string $path
     * @param string $default
     *
     * @return mixed|null
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     *
     */
    public function aroundGet(
        \Magento\Indexer\Model\Config\Data $subject,
        \Closure $proceed,
        $path = null,
        $default = null
    ) {
        $data = $proceed($path, $default);

        if (!$this->_helper->isEnabled()) {
            $indexerId = \Magento\Catalog\Model\Indexer\Product\Flat\Processor::INDEXER_ID;
            if ((!isset($path) || !$path) && isset($data[$indexerId])) {
                unset($data[$indexerId]);
            } elseif (isset($path)) {
               list($firstKey, ) = explode('/', $path);
                if ($firstKey == $indexerId) {
                    $data = isset($default) ? $default : null;
                }
            }
        }

        return $data;
    }
}
