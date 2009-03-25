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
 * Catalog Events edit form
 *
 * @category   Enterprise
 * @package    Enterprise_CatalogEvent
 */
class Enterprise_CatalogEvent_Block_Adminhtml_Event_Edit_Form extends Mage_Adminhtml_Block_Widget_Form
{

    /**
     * Return form action url
     *
     * @return string
     */
    public function getActionUrl()
    {
        return $this->getUrl('*/*/save', array('_current' => true));
    }

    /**
     * Prepares event edit form
     *
     * @return Enterprise_CatalogEvent_Block_Adminhtml_Event_Edit_Form
     */
    protected function _prepareForm()
    {
        $form = new Varien_Data_Form(
            array(
                'id' => 'edit_form',
                'action' => $this->getActionUrl(),
                'method' => 'post'
            )
        );

        $form->setHtmlIdPrefix('event_edit_');

        $dataHelper = $this->helper('enterprise_catalogevent/adminhtml_event');
        /* @var $dataHelper Enterprise_CatalogEvent_Helper_Adminhtml_Event */

        $fieldset = $form->addFieldset('general_fieldset',
            array(
                'legend' => Mage::helper('enterprise_catalogevent')->__('Event Information'),
                'class' => 'fieldset-wide'
            )
        );

        $currentCategory = Mage::getModel('catalog/category')
            ->load($this->getEvent()->getCategoryId());


        $fieldset->addField('category_name', 'note',
            array(
                'id' => 'category_span',
                'label' => Mage::helper('enterprise_catalogevent')->__('Category')
            )
        );

        $dateFormatIso = Mage::app()->getLocale()->getDateTimeFormat(
            Mage_Core_Model_Locale::FORMAT_TYPE_MEDIUM
        );

        $fieldset->addField('date_start', 'date',
            array(

                'label' => Mage::helper('enterprise_catalogevent')->__('Start Date'),
                'name' => 'date_start',
                'required' => true, 'time' => true,
                'image' => $this->getSkinUrl('images/grid-cal.gif'),
                'format' => $dateFormatIso
            ));

        $fieldset->addField('date_end', 'date',
            array(
                'label' => Mage::helper('enterprise_catalogevent')->__('End Date'),
                'name' => 'date_end', 'required' => true,
                'time' => true,
                'image' => $this->getSkinUrl('images/grid-cal.gif'),
                'format' => $dateFormatIso
            ));

        $statuses = array(
            Enterprise_CatalogEvent_Model_Event::STATUS_UPCOMING => Mage::helper('enterprise_catalogevent')->__('Upcoming'),
            Enterprise_CatalogEvent_Model_Event::STATUS_OPEN => Mage::helper('enterprise_catalogevent')->__('Open'),
            Enterprise_CatalogEvent_Model_Event::STATUS_CLOSED => Mage::helper('enterprise_catalogevent')->__('Closed')
        );

        $fieldset->addField('display_state_array', 'checkboxes',
            array(
                'label' => Mage::helper('enterprise_catalogevent')->__('Display Ticker On'),
                'name' => 'display_state[]',
                'values' => array(
                    Enterprise_CatalogEvent_Model_Event::DISPLAY_CATEGORY_PAGE => Mage::helper('enterprise_catalogevent')->__('Category Page'),
                    Enterprise_CatalogEvent_Model_Event::DISPLAY_PRODUCT_PAGE => Mage::helper('enterprise_catalogevent')->__('Product Page')
                )
            ));


        if ($this->getEvent()->getId()) {
            $fieldset->addField('status', 'note',
                array(

                    'label' => Mage::helper('enterprise_catalogevent')->__('Status'),
                    'text' => ($this->getEvent()->getStatus() ? $statuses[$this->getEvent()->getStatus()] : $statuses[Enterprise_CatalogEvent_Model_Event::STATUS_UPCOMING])
            ));
        }


        if ($sessionData = Mage::getSingleton('adminhtml/session')->getEventData(true)) {
            $form->setValues($sessionData);
        } else {
            $form->setValues($this->getEvent()->getData());
        }



        if ($this->getEvent()->getDateStart() && !$sessionData) {
            $date = Mage::app()->getLocale()->date(
                $this->getEvent()->getDateStart(),
                Varien_Date::DATETIME_INTERNAL_FORMAT
            );
            $form->getElement('date_start')->setValue($date);
        }

        if ($this->getEvent()->getDateEnd() && !$sessionData) {
            $date = Mage::app()->getLocale()->date(
                $this->getEvent()->getDateEnd(),
                Varien_Date::DATETIME_INTERNAL_FORMAT
            );
            $form->getElement('date_end')->setValue($date);
        }

        if (is_array($form->getElement('display_state_array')->getValue())) {
            $form->getElement('display_state_array')->setValue(array_merge(
                  array((string) 0), // Work around for checkbox checked bug
                  $form->getElement('display_state_array')->getValue()
            ));
        }

        if ($currentCategory && $this->getEvent()->getId()) {
            $form->getElement('category_name')->setText(
                '<a href="' . Mage::helper('adminhtml')->getUrl('adminhtml/catalog_category/edit',
                                                                array('clear' => 1, 'id' => $currentCategory->getId()))
                . '">' . $currentCategory->getName() . '</a>'
            );
        } else {
            $form->getElement('category_name')->setText(
                '<a href="' . $this->getParentBlock()->getBackUrl()
                . '">' . $currentCategory->getName() . '</a>'
            );
        }

        $form->setUseContainer(true);
        $this->setForm($form);

        return parent::_prepareForm();
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
