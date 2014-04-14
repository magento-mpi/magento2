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
namespace Magento\CatalogEvent\Block\Catalog\Category;

class Event extends \Magento\CatalogEvent\Block\Event\AbstractEvent implements \Magento\View\Block\IdentityInterface
{
    /**
     * Core registry
     *
     * @var \Magento\Registry
     */
    protected $_coreRegistry;

    /**
     * Catalog event data
     *
     * @var \Magento\CatalogEvent\Helper\Data
     */
    protected $_catalogEventData;

    /**
     * @param \Magento\View\Element\Template\Context $context
     * @param \Magento\Locale\ResolverInterface $localeResolver
     * @param \Magento\Registry $registry
     * @param \Magento\CatalogEvent\Helper\Data $catalogEventData
     * @param array $data
     */
    public function __construct(
        \Magento\View\Element\Template\Context $context,
        \Magento\Locale\ResolverInterface $localeResolver,
        \Magento\Registry $registry,
        \Magento\CatalogEvent\Helper\Data $catalogEventData,
        array $data = array()
    ) {
        $this->_coreRegistry = $registry;
        $this->_catalogEventData = $catalogEventData;
        parent::__construct($context, $localeResolver, $data);
    }

    /**
     * Return current category event
     *
     * @return \Magento\CatalogEvent\Block\Catalog\Category\Event
     */
    public function getEvent()
    {
        return $this->getCategory()->getEvent();
    }

    /**
     * Return current category
     *
     * @return \Magento\Catalog\Model\Category
     */
    public function getCategory()
    {
        return $this->_coreRegistry->registry('current_category');
    }

    /**
     * Return category url
     *
     * @param \Magento\Framework\Data\Tree\Node $category
     * @return string
     */
    public function getCategoryUrl($category = null)
    {
        if ($category === null) {
            $category = $this->getCategory();
        }

        return $category->getUrl();
    }

    /**
     * Check availability to display event block
     *
     * @return boolean
     */
    public function canDisplay()
    {
        return $this->_catalogEventData->isEnabled() &&
            $this->getEvent() &&
            $this->getEvent()->canDisplayCategoryPage();
    }

    /**
     * Return identifiers for produced content
     *
     * @return array
     */
    public function getIdentities()
    {
        return $this->getCategory()->getIdentities();
    }
}
