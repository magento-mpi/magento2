<?php
/**
 *
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Solr\Model\Layer\Search\AvailabilityFlag;

class Plugin
{
    /**
     * @var \Magento\Solr\Helper\Data
     */
    protected $helper;

    /**
     * @param \Magento\Solr\Helper\Data $helper
     */
    public function __construct(\Magento\Solr\Helper\Data $helper)
    {
        $this->helper = $helper;
    }

    /**
     * Check if block should be enabled
     *
     * @param \Magento\Catalog\Model\Layer\Search\AvailabilityFlag $subject
     * @param \Closure $proceed
     * @param \Magento\Catalog\Model\Layer $layer
     * @param array $filters
     * @return bool
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    public function aroundIsEnabled(
        \Magento\Catalog\Model\Layer\Search\AvailabilityFlag $subject,
        \Closure $proceed,
        $layer,
        $filters
    ) {
        if ($this->helper->isThirdPartSearchEngine() && $this->helper->isActiveEngine()) {
            return $this->canShowOptions($filters) || count($layer->getState()->getFilters());
        }
        return $proceed($layer, $filters);
    }

    /**
     * @param array $filters
     * @return bool
     */
    protected function canShowOptions($filters)
    {
        foreach ($filters as $filter) {
            if ($filter->getItemsCount()) {
                return true;
            }
        }

        return false;
    }
}
