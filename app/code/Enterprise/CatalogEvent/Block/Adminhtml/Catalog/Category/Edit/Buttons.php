<?php
/**
 * {license_notice}
 *
 * @category    Enterprise
 * @package     Enterprise_CatalogEvent
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Catalog Events edit form select categories
 *
 * @category   Enterprise
 * @package    Enterprise_CatalogEvent
 */

class Enterprise_CatalogEvent_Block_Adminhtml_Catalog_Category_Edit_Buttons
    extends Magento_Adminhtml_Block_Catalog_Category_Abstract
{
    /**
     * Catalog event data
     *
     * @var Enterprise_CatalogEvent_Helper_Data
     */
    protected $_catalogEventData = null;

    /**
     * Adminhtml data
     *
     * @var Magento_Adminhtml_Helper_Data
     */
    protected $_adminhtmlData = null;

    /**
     * @param Magento_Adminhtml_Helper_Data $adminhtmlData
     * @param Enterprise_CatalogEvent_Helper_Data $catalogEventData
     * @param Magento_Core_Helper_Data $coreData
     * @param Magento_Backend_Block_Template_Context $context
     * @param array $data
     */
    public function __construct(
        Magento_Adminhtml_Helper_Data $adminhtmlData,
        Enterprise_CatalogEvent_Helper_Data $catalogEventData,
        Magento_Core_Helper_Data $coreData,
        Magento_Backend_Block_Template_Context $context,
        array $data = array()
    ) {
        $this->_adminhtmlData = $adminhtmlData;
        $this->_catalogEventData = $catalogEventData;
        parent::__construct($coreData, $context, $data);
    }

    /**
     * Retrieve category event
     *
     * @return Enterprise_CatalogEvent_Model_Event
     */
    public function getEvent()
    {
        if (!$this->hasData('event')) {
            $collection = Mage::getModel('Enterprise_CatalogEvent_Model_Event')->getCollection()
                ->addFieldToFilter('category_id', $this->getCategoryId());

            $event = $collection->getFirstItem();
            $this->setData('event', $event);
        }

        return $this->getData('event');
    }

    /**
     * Add buttons on category edit page
     *
     * @return Enterprise_CatalogEvent_Block_Adminhtml_Catalog_Category_Edit_Buttons
     */
    public function addButtons()
    {
        if ($this->_catalogEventData->isEnabled()
            && $this->_authorization->isAllowed('Enterprise_CatalogEvent::events')
            && $this->getCategoryId() && $this->getCategory()->getLevel() > 1) {
            if ($this->getEvent() && $this->getEvent()->getId()) {
                $url = $this->_adminhtmlData->getUrl('*/catalog_event/edit', array(
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
                $url = $this->_adminhtmlData->getUrl('*/catalog_event/new', array(
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
