<?php
/**
 * {license_notice}
 *
 * @category    Enterprise
 * @package     Enterprise_Tag
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Block limiters for AdminGws module
 */
class Enterprise_Tag_Model_AdminGws_Blocks extends Magento_AdminGws_Model_Observer_Abstract
{
    /**
     * Remove control buttons if user does not have exclusive access to current tag
     *
     * @param Magento_Event_Observer $observer
     * @return Enterprise_Tag_Model_AdminGws_Blocks
     */
    public function removeTagButtons($observer)
    {
        $model = Mage::registry('current_tag');
        if ($model && $model->getId()) {
            $storeIds = (array)$model->getVisibleInStoreIds();
            $storeIds = array_filter($storeIds); // remove admin store with id 0
            if (!$this->_role->hasExclusiveStoreAccess((array)$storeIds)) {
                $block = $observer->getEvent()->getBlock();
                $block->removeButton('save');
                $block->removeButton('save_and_edit_button');
                $block->removeButton('delete');
            }
        }

        return $this;
    }

    /**
     * Remove massactions for limited user
     *
     * @param Magento_Event_Observer $observer
     * @return Enterprise_Tag_Model_AdminGws_Blocks
     */
    public function removeTagGridActions($observer)
    {
        $massBlock = $observer->getEvent()->getBlock()->getMassactionBlock();
        /* @var $massBlock Magento_Adminhtml_Block_Widget_Grid_Massaction */
        if ($massBlock) {
            $massBlock->removeItem('delete');
        }

        return $this;
    }

    /**
     * Disable fields in edit form if user does not have exclusive access to current tag
     *
     * @param Magento_Event_Observer $observer
     * @return Enterprise_Tag_Model_AdminGws_Blocks
     */
    public function disableTagEditFormFields($observer)
    {
        $model = Mage::registry('current_tag');
        if ($model && $model->getId()) {
            $storeIds = (array)$model->getVisibleInStoreIds();
            $storeIds = array_filter($storeIds); // remove admin store with id 0
            if (!$this->_role->hasExclusiveStoreAccess((array)$storeIds)) {
                $elements = $observer->getEvent()->getBlock()->getForm()->getElement('base_fieldset')->getElements();
                $elements->searchById('name')->setReadonly(true, true);
                $elements->searchById('status')->setReadonly(true, true);
                $elements->searchById('base_popularity')->setReadonly(true, true);
            }
        }
        return $this;
    }
}
