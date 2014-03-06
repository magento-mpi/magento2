<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */


/**
 * Catalog Event homepage block
 */
namespace Magento\CatalogEvent\Block\Event;

use Magento\Catalog\Helper\Category as HelperCategory;
use Magento\Catalog\Model\Category;
use Magento\CatalogEvent\Helper\Data;
use Magento\CatalogEvent\Model\Event;
use Magento\CatalogEvent\Model\Resource\Event\Collection;
use Magento\CatalogEvent\Model\Resource\Event\CollectionFactory;
use Magento\View\Element\Template\Context;

class Lister extends AbstractEvent
{
    /**
     * Events list
     *
     * @var Event[]
     */
    protected $_events = null;

    /**
     * Catalog event data
     *
     * @var Data
     */
    protected $_catalogEventData;

    /**
     * Event collection factory
     *
     * @var CollectionFactory
     */
    protected $_eventCollectionFactory;

    /**
     * @var HelperCategory
     */
    protected $_categoryHelper;

    /**
     * @param Context $context
     * @param \Magento\Locale\ResolverInterface $localeResolver
     * @param Data $catalogEventData
     * @param CollectionFactory $eventCollectionFactory
     * @param HelperCategory $categoryHelper
     * @param array $data
     */
    public function __construct(
        Context $context,
        \Magento\Locale\ResolverInterface $localeResolver,
        Data $catalogEventData,
        CollectionFactory $eventCollectionFactory,
        HelperCategory $categoryHelper,
        array $data = array()
    ) {
        $this->_categoryHelper = $categoryHelper;
        $this->_catalogEventData = $catalogEventData;
        $this->_eventCollectionFactory = $eventCollectionFactory;
        parent::__construct($context, $localeResolver, $data);
    }

    /**
     * Retrieve html id
     *
     * @return string
     */
    public function getHtmlId()
    {
        if (!$this->hasData('html_id')) {
            $this->setData('html_id', 'id_' . md5(uniqid('catalogevent', true)));
        }

        return $this->getData('html_id');
    }

    /**
     * Check whether the block can be displayed
     *
     * @return bool
     */
    public function canDisplay()
    {
        return $this->_catalogEventData->isEnabled()
            && $this->_storeConfig->getConfigFlag('catalog/magento_catalogevent/lister_output')
            && (count($this->getEvents()) > 0);
    }

    /**
     * Retrieve categories with events
     *
     * @return Event[]
     */
    public function getEvents()
    {
        if ($this->_events === null) {
            $this->_events = array();
            $categories = $this->_categoryHelper->getStoreCategories('position', true, false);
            if (($categories instanceof \Magento\Eav\Model\Entity\Collection\AbstractCollection) ||
                ($categories instanceof \Magento\Core\Model\Resource\Db\Collection\AbstractCollection)) {
                $allIds = $categories->getAllIds();
            } else {
                $allIds = array();
            }

            if (!empty($allIds)) {
                /** @var Collection $eventCollection */
                $eventCollection = $this->_eventCollectionFactory->create();
                $eventCollection->addFieldToFilter('category_id', array('in' => $allIds))
                    ->addVisibilityFilter()
                    ->addImageData()
                    ->addSortByStatus()
                ;

                $categories->addIdFilter(
                    $eventCollection->getColumnValues('category_id')
                );

                foreach ($categories as $category) {
                    $event = $eventCollection->getItemByColumnValue('category_id', $category->getId());
                    if ($category->getIsActive()) {
                        $event->setCategory($category);
                    } else {
                        $eventCollection->removeItemByKey($event->getId());
                    }
                }

                foreach ($eventCollection as $event) {
                    $this->_events[] = $event;
                }
            }
        }

        return $this->_events;
    }

    /**
     * Retrieve category url
     *
     * @param Category $category
     * @return string
     */
    public function getCategoryUrl($category)
    {
        return $this->_categoryHelper->getCategoryUrl($category);
    }

    /**
     * Retrieve catalog category image url
     *
     * @param Event $event
     * @return string
     */
    public function getEventImageUrl($event)
    {
        return $this->_catalogEventData->getEventImageUrl($event);
    }

    /**
     * Get items number to show per page
     *
     * @return int
     */
    public function getPageSize()
    {
        if ($this->hasData('limit') && is_numeric($this->getData('limit'))) {
            $pageSize = (int) $this->_getData('limit');
        } else {
            $pageSize = (int)$this->_storeConfig->getConfig('catalog/magento_catalogevent/lister_widget_limit');
        }
        return max($pageSize, 1);
    }

    /**
     * Get items number to scroll
     *
     * @return int
     */
    public function getScrollSize()
    {
        if ($this->hasData('scroll') && is_numeric($this->getData('scroll'))) {
            $scrollSize = (int) $this->_getData('scroll');
        } else {
            $scrollSize = (int)$this->_storeConfig->getConfig('catalog/magento_catalogevent/lister_widget_scroll');
        }
        return  min(max($scrollSize, 1), $this->getPageSize());
    }

    /**
     * Output content, if allowed
     *
     * @return string
     */
    protected function _toHtml()
    {
        if (!$this->canDisplay()) {
            return '';
        }
        return parent::_toHtml();
    }
}
