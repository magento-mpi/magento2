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
 * Staging History Item View
 *
 *
 * @author     Magento Core Team <core@magentocommerce.com>
 */
class Enterprise_Staging_Block_Adminhtml_Log_View_Form extends Mage_Adminhtml_Block_Widget_Form
{
    public function __construct()
    {
        parent::__construct();
        $this->setTemplate('log/view.phtml');
    }

    /**
     * Prepare form before rendering HTML
     *
     * @return Enterprise_Staging_Block_Manage_Staging_Edit_Tabs_Website
     */
    protected function _prepareForm()
    {
        $form       = new Varien_Data_Form();
        $config     = Mage::getSingleton('Enterprise_Staging_Model_Staging_Config');
        $log        = $this->getLog();
        $staging    = $log->getStaging();
        $fieldset   = $form->addFieldset('general_fieldset',
            array('legend' => Mage::helper('Enterprise_Staging_Helper_Data')->__('General Information')));

        $fieldset->addField('created_at', 'label', array(
            'label'     => Mage::helper('Enterprise_Staging_Helper_Data')->__('Logged At'),
            'value'     => $this->formatDate($log->getCreatedAt(), 'medium', true)
        ));

        $fieldset->addField('action', 'label', array(
            'label'     => Mage::helper('Enterprise_Staging_Helper_Data')->__('Action'),
            'value'     => Mage::helper('Enterprise_Staging_Helper_Data')->__($config->getActionLabel($log->getAction()))
        ));

        $fieldset->addField('status', 'label', array(
            'label'     => Mage::helper('Enterprise_Staging_Helper_Data')->__('Status'),
            'value'     => Mage::helper('Enterprise_Staging_Helper_Data')->__($config->getStatusLabel($log->getStatus()))
        ));

        $additionalData = $log->getAdditionalData();
        if (!empty($additionalData)) {
            $additionalData = unserialize($additionalData);
            if (is_array($additionalData)) {
                if (isset($additionalData['schedule_date'])) {
                    $fieldset->addField('schedule_date', 'label', array(
                        'label' => Mage::helper('Enterprise_Staging_Helper_Data')->__('Schedule Date'),
                        'value' => Mage::helper('Mage_Core_Helper_Data')->formatDate($additionalData['schedule_date'],
                            Mage_Core_Model_Locale::FORMAT_TYPE_MEDIUM, true)
                    ));
                }
                if(isset($additionalData['action_before_reset'])) {
                   $fieldset->addField('action_before_reset', 'label', array(
                        'label'     => Mage::helper('Enterprise_Staging_Helper_Data')->__('Action Before Resetting'),
                        'value'     => Mage::helper('Enterprise_Staging_Helper_Data')->__($config->getActionLabel($additionalData['action_before_reset']))
                    ));
                }
            }
        }
        if ($log->getAction() == Enterprise_Staging_Model_Staging_Config::ACTION_UNSCHEDULE_MERGE) {
            $mergerUrl = $this->getUrl('*/staging_manage/merge', array('id' => $staging->getId()));
            $fieldset->addField('link_to_staging_merge', 'link', array(
                'href'      => $mergerUrl,
                'label'     => Mage::helper('Enterprise_Staging_Helper_Data')->__('Scheduled Merger'),
                'value'     => $mergerUrl
            ));
        }

        $form->addFieldNameSuffix($this->getFieldNameSuffix());
        $this->setForm($form);

        return parent::_prepareForm();
    }

    public function getInformationHtml(Varien_Object $log)
    {
        return $this->getParentBlock()->getInformationHtml($log);
    }

    /**
     * Retrieve currently viewing log
     *
     * @return Enterprise_Staging_Model_Staging_Log
     */
    public function getLog()
    {
        if (!($this->getData('log') instanceof Enterprise_Staging_Model_Staging_Log)) {
            $this->setData('log', Mage::registry('log'));
        }
        return $this->getData('log');
    }
}
