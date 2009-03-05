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
 * Catalog Events edit page
 *
 * @category   Enterprise
 * @package    Enterprise_CatalogEvent
 */
class Enterprise_CatalogEvent_Block_Adminhtml_Event_Edit
    extends Mage_Adminhtml_Block_Widget_Form_Container
{
    protected $_objectId = 'event_id';
    protected $_blockGroup = 'enterprise_catalogevent';
    protected $_controller = 'adminhtml_event';

    /**
     * Prepare catalog event form or category selector
     *
     * @return Enterprise_CatalogEvent_Block_Adminhtml_Event_Edit
     */
    protected function _prepareLayout()
    {
        parent::_prepareLayout();

        if (!$this->getEvent()->getId() && !$this->getEvent()->getCategoryId()) {
            $this->setChild('form', $this->getLayout()->createBlock($this->_blockGroup . '/' . $this->_controller . '_' . $this->_mode . '_category'));
        }

        return $this;
    }


    /**
     * Retrieve form back url
     *
     * @return string
     */
    public function getBackUrl()
    {
        if ($this->getRequest()->getParam('category')) {
            return $this->getUrl('*/catalog_category/edit',
                                array('clear' => 1, 'id' => $this->getEvent()->getCategoryId()));
        } elseif (!$this->getEvent()->getId() && $this->getEvent()->getCategoryId()) {
            return $this->getUrl('*/*/new',
                                 array('_current' => true, 'category_id' => null));
        }

        return parent::getBackUrl();
    }


    /**
     * Retrieve form container header
     *
     * @return string
     */
    public function getHeaderText()
    {
        if ($this->getEvent()->getId()) {
            return Mage::helper('enterprise_catalogevent')->__('Edit Event');
        }
        else {
            return Mage::helper('enterprise_catalogevent')->__('New Event');
        }
    }

    /**
     * Retrive catalog event model
     *
     * @return Enterprise_CatalogEvent_Model_Event
     */
    public function getEvent()
    {
        return Mage::registry('enterprise_catalogevent_event');
    }

}
