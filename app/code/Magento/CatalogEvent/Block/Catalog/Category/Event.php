<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_CatalogEvent
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Catalog Event on category page
 *
 * @category   Magento
 * @package    Magento_CatalogEvent
 */
namespace Magento\CatalogEvent\Block\Catalog\Category;

class Event extends \Magento\CatalogEvent\Block\Event\AbstractEvent
{
    /**
     * Return current category event
     *
     * @return Magento_CategoryEvent_Model_Event
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
        return \Mage::registry('current_category');
    }

    /**
     * Return category url
     *
     * @param \Magento\Data\Tree\Node $category
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
        return \Mage::helper('Magento\CatalogEvent\Helper\Data')->isEnabled() &&
               $this->getEvent() &&
               $this->getEvent()->canDisplayCategoryPage();
    }
}
