<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Catalog Events edit form select categories
 */
class Magento_CatalogEvent_Block_Adminhtml_Catalog_Category_Edit_Buttons
    extends Magento_Adminhtml_Block_Catalog_Category_Abstract
{
    /**
     * Factory for event collections
     *
     * @var Magento_CatalogEvent_Model_Resource_Event_CollectionFactory
     */
    protected $_eventCollectionFactory;

    /**
     * Construct
     *
     * @param Magento_Core_Helper_Data $coreData
     * @param Magento_Backend_Block_Template_Context $context
     * @param Magento_Core_Model_Registry $registry
     * @param Magento_CatalogEvent_Model_Resource_Event_CollectionFactory $eventCollectionFactory
     * @param array $data
     */
    public function __construct(
        Magento_Core_Helper_Data $coreData,
        Magento_Backend_Block_Template_Context $context,
        Magento_Core_Model_Registry $registry,
        Magento_CatalogEvent_Model_Resource_Event_CollectionFactory $eventCollectionFactory,
        array $data = array()
    ) {
        parent::__construct($coreData, $context, $registry, $data);

        $this->_eventCollectionFactory = $eventCollectionFactory;
    }

    /**
     * Retrieve category event
     *
     * @return Magento_CatalogEvent_Model_Event
     */
    public function getEvent()
    {
        if (!$this->hasData('event')) {
            /** @var Magento_CatalogEvent_Model_Resource_Event_Collection $collection */
            $collection = $this->_eventCollectionFactory->create()
                ->addFieldToFilter('category_id', $this->getCategoryId());

            $event = $collection->getFirstItem();
            $this->setData('event', $event);
        }

        return $this->getData('event');
    }

    /**
     * Add buttons on category edit page
     *
     * @return Magento_CatalogEvent_Block_Adminhtml_Catalog_Category_Edit_Buttons
     */
    public function addButtons()
    {
        if ($this->helper('Magento_CatalogEvent_Helper_Data')->isEnabled()
            && $this->_authorization->isAllowed('Magento_CatalogEvent::events')
            && $this->getCategoryId() && $this->getCategory()->getLevel() > 1) {
            if ($this->getEvent() && $this->getEvent()->getId()) {
                $url = $this->helper('Magento_Adminhtml_Helper_Data')->getUrl('*/catalog_event/edit', array(
                            'id' => $this->getEvent()->getId(),
                            'category' => 1
                ));
                $this->getParentBlock()->getChildBlock('form')
                    ->addAdditionalButton('edit_event', array(
                        'label' => __('Edit Event...'),
                        'class' => 'save',
                        'onclick'   => 'setLocation(\''. $url .'\')'
                    ));
            } else {
                $url = $this->helper('Magento_Adminhtml_Helper_Data')->getUrl('*/catalog_event/new', array(
                        'category_id' => $this->getCategoryId(),
                        'category' => 1
                ));
                $this->getParentBlock()->getChildBlock('form')
                    ->addAdditionalButton('add_event', array(
                        'label' => __('Add Event...'),
                        'class' => 'add',
                        'onclick' => 'setLocation(\''. $url .'\')'
                    ));
            }
        }
        return $this;
    }
}
