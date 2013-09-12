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
class Magento_CatalogEvent_Block_Event_Lister extends Magento_CatalogEvent_Block_Event_Abstract
{
    /**
     * Events list
     *
     * @var array
     */
    protected $_events = null;

    /**
     * Event collection factory
     *
     * @var Magento_CatalogEvent_Model_Resource_Event_CollectionFactory
     */
    protected $_eventCollectionFactory;

    /**
     * Construct
     *
     * @param Magento_Core_Block_Template_Context $context
     * @param Magento_Core_Model_LocaleInterface $locale
     * @param Magento_CatalogEvent_Model_Resource_Event_CollectionFactory $eventCollectionFactory
     * @param array $data
     */
    public function __construct(
        Magento_Core_Block_Template_Context $context,
        Magento_Core_Model_LocaleInterface $locale,
        Magento_CatalogEvent_Model_Resource_Event_CollectionFactory $eventCollectionFactory,
        array $data = array()
    ) {
        parent::__construct($context, $locale, $data);

        $this->_eventCollectionFactory = $eventCollectionFactory;
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
        return Mage::helper('Magento_CatalogEvent_Helper_Data')->isEnabled()
            && Mage::getStoreConfigFlag('catalog/magento_catalogevent/lister_output')
            && (count($this->getEvents()) > 0);
    }

    /**
     * Retrieve categories with events
     *
     * @return array
     */
    public function getEvents()
    {
        if ($this->_events === null) {
            $this->_events = array();
            $categories = $this->helper('Magento_Catalog_Helper_Category')->getStoreCategories('position', true, false);
            if (($categories instanceof Magento_Eav_Model_Entity_Collection_Abstract) ||
                ($categories instanceof Magento_Core_Model_Resource_Db_Collection_Abstract)) {
                $allIds = $categories->getAllIds();
            } else {
                $allIds = array();
            }

            if (!empty($allIds)) {
                /** @var Magento_CatalogEvent_Model_Resource_Event_Collection $eventCollection */
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
     * Retreive category url
     *
     * @param Magento_Catalog_Model_Category $category
     * @return string
     */
    public function getCategoryUrl($category)
    {
        return $this->helper('Magento_Catalog_Helper_Category')->getCategoryUrl($category);
    }

    /**
     * Retrieve catalog category image url
     *
     * @param Magento_CatalogEvent_Model_Event $event
     * @return string
     */
    public function getEventImageUrl($event)
    {
        return $this->helper('Magento_CatalogEvent_Helper_Data')->getEventImageUrl($event);
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
        }
        else {
            $pageSize = (int)Mage::getStoreConfig('catalog/magento_catalogevent/lister_widget_limit');
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
        }
        else {
            $scrollSize = (int)Mage::getStoreConfig('catalog/magento_catalogevent/lister_widget_scroll');
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
