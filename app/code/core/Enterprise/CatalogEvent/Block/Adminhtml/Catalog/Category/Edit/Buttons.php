<?php
/**
 * Magento
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://opensource.org/licenses/osl-3.0.php
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@magentocommerce.com so we can send you a copy immediately.
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade Magento to newer
 * versions in the future. If you wish to customize Magento for your
 * needs please refer to http://www.magentocommerce.com for more information.
 *
 * @category   Enterprise
 * @package    Enterprise_CatalogEvent
 * @copyright  Copyright (c) 2008 Irubin Consulting Inc. DBA Varien (http://www.varien.com)
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/**
 * Catalog Events edit form select categories
 *
 * @category   Enterprise
 * @package    Enterprise_CatalogEvent
 */

class Enterprise_CatalogEvent_Block_Adminhtml_Catalog_Category_Edit_Buttons extends Mage_Adminhtml_Block_Catalog_Category_Abstract
{
    /**
     * Retrieve category event
     *
     * @return Enterprise_CatalogEvent_Model_Category
     */
    public function getEvent()
    {
        if (!$this->hasData('event')) {
            $collection = Mage::getModel('enterprise_catalogevent/event')->getCollection()
                ->addFieldToFilter('category_id', $this->getCategoryId());

            $event = $collection->getFirstItem();
            $this->setData('event', $event);
        }

        return $this->getData('event');
    }

    /**
     * Add buttons on category edit page
     *
     * @return Enterprise_CatalogEvent_Block_Adminhtml_Catalog_Category_Buttons
     */
    public function addButtons()
    {
        if (Mage::getSingleton('admin/session')->isAllowed('catalog/events') &&
            $this->getCategoryId() && $this->getCategory()->getLevel() > 1) {
            if ($this->getEvent() && $this->getEvent()->getId()) {
                $url = $this->helper('adminhtml')->getUrl('*/catalog_event/edit', array(
                            'id' => $this->getEvent()->getId(),
                            'category' => 1
                ));
                $this->getParentBlock()->getChild('form')
                    ->addAdditionalButton('edit_event', array(
                        'label' => $this->helper('enterprise_catalogevent')->__('Edit Catalog Event'),
                        'class' => 'save',
                        'onclick'   => 'setLocation(\''. $url .'\')'
                    ));
            } else {
                $url = $this->helper('adminhtml')->getUrl('*/catalog_event/new', array(
                        'category_id' => $this->getCategoryId(),
                        'category' => 1
                ));
                $this->getParentBlock()->getChild('form')
                    ->addAdditionalButton('add_event', array(
                        'label' => $this->helper('enterprise_catalogevent')->__('Add Catalog Event'),
                        'class' => 'add',
                        'onclick' => 'setLocation(\''. $url .'\')'
                    ));
            }
        }
        return $this;
    }
}