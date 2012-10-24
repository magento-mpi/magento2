<?php
/**
 * {license_notice}
 *
 * @category    Enterprise
 * @package     Enterprise_Staging
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Staging schedule configuration tab
 *
 * @author     Magento Core Team <core@magentocommerce.com>
 */
class Enterprise_Staging_Block_Adminhtml_Staging_Edit_Tabs_Schedule extends Mage_Adminhtml_Block_Widget_Form
{
    /**
     * Constructor
     */
    public function __construct()
    {
        parent::__construct();

        $this->setFieldNameSuffix('staging');
    }

    /**
     * return html content
     *
     * @return string
     */
    protected function _toHtml()
    {
        $dateFormat = Mage::app()->getLocale()->getDateFormat(Mage_Core_Model_Locale::FORMAT_TYPE_SHORT);
        $timeFormat = Mage::app()->getLocale()->getTimeFormat(Mage_Core_Model_Locale::FORMAT_TYPE_SHORT);

        $form = new Varien_Data_Form();

        $fieldset = $form->addFieldset('general_fieldset',
            array('legend' => Mage::helper('Enterprise_Staging_Helper_Data')->__('Staging Merge Schedule Configuration')));

        $element = $fieldset->addField('schedule_merge_later', 'date', array(
            'label'     => Mage::helper('Enterprise_Staging_Helper_Data')->__('Set Staging Merge Date'),
            'title'     => Mage::helper('Enterprise_Staging_Helper_Data')->__('Set Staging Merge Date'),
            'name'      => 'schedule_merge_later',
            'date_format' => $dateFormat,
            'time_format' => $timeFormat,
            'image'     => $this->getSkinUrl('images/grid-cal.gif')
        ));

        return $element->getHtml();
    }
}
