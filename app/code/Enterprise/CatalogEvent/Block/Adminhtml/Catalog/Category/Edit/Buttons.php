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
        if ($this->helper('Enterprise_CatalogEvent_Helper_Data')->isEnabled()
            && $this->_authorization->isAllowed('Enterprise_CatalogEvent::events')
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
