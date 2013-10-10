<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Catalog Event on category page
 */
namespace Magento\CatalogEvent\Block\Catalog\Product;

class Event extends \Magento\CatalogEvent\Block\Event\AbstractEvent
{
    /**
     * Core registry
     *
     * @var \Magento\Core\Model\Registry
     */
    protected $_coreRegistry;

    /**
     * Catalog event data
     *
     * @var \Magento\CatalogEvent\Helper\Data
     */
    protected $_catalogEventData;

    /**
     * Construct
     *
     * @param \Magento\Core\Helper\Data $coreData
     * @param \Magento\Core\Block\Template\Context $context
     * @param \Magento\Core\Model\LocaleInterface $locale
     * @param \Magento\Core\Model\Registry $registry
     * @param \Magento\CatalogEvent\Helper\Data $catalogEventData
     * @param array $data
     */
    public function __construct(
        \Magento\Core\Helper\Data $coreData,
        \Magento\Core\Block\Template\Context $context,
        \Magento\Core\Model\LocaleInterface $locale,
        \Magento\Core\Model\Registry $registry,
        \Magento\CatalogEvent\Helper\Data $catalogEventData,
        array $data = array()
    ) {
        parent::__construct($coreData, $context, $locale, $data);
        
        $this->_coreRegistry = $registry;
        $this->_catalogEventData = $catalogEventData;
    }

    /**
     * Return current category event
     *
     * @return \Magento\CategoryEvent\Model\Event
     */
    public function getEvent()
    {
        if ($this->getProduct()) {
            return $this->getProduct()->getEvent();
        }

        return false;
    }

    /**
     * Return current category
     *
     * @return \Magento\Catalog\Model\Category
     */
    public function getProduct()
    {
        return $this->_coreRegistry->registry('current_product');
    }

    /**
     * Check availability to display event block
     *
     * @return boolean
     */
    public function canDisplay()
    {
        return $this->_catalogEventData->isEnabled()
            && $this->getProduct()
            && $this->getEvent()
            && $this->getEvent()->canDisplayProductPage()
            && !$this->getProduct()->getEventNoTicker();
    }
}
