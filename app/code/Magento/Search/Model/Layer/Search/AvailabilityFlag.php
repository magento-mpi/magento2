<?php
/**
 *
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Search\Model\Layer\Search;

use Magento\CatalogSearch\Model\Resource\EngineProvider;

class AvailabilityFlag extends \Magento\CatalogSearch\Model\Layer\AvailabilityFlag
{
    /**
     * @var \Magento\Search\Helper\Data
     */
    protected $helper;

    /**
     * @param \Magento\Core\Model\StoreManagerInterface $storeManager
     * @param EngineProvider $engineProvider
     * @param \Magento\Search\Helper\Data $helper
     */
    public function __construct(
        \Magento\Core\Model\StoreManagerInterface $storeManager,
        EngineProvider $engineProvider,
        \Magento\Search\Helper\Data $helper
    ) {
        $this->helper = $helper;
        parent::__construct($storeManager, $engineProvider);
    }


    /**
     * @param \Magento\Catalog\Model\Layer $layer
     * @param array $filters
     * @return bool
     */
    public function isEnabled($layer, $filters)
    {
        if ($this->helper->isThirdPartSearchEngine() && $this->helper->isActiveEngine()) {
            return \Magento\Catalog\Model\Layer\Category\AvailabilityFlag::isEnabled($layer, $filters);
        }
        return parent::isEnabled($layer, $filters);
    }
} 
