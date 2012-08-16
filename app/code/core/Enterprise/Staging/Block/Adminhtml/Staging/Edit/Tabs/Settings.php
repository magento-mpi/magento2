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
 * Create Staging settings tab
 *
 * @author     Magento Core Team <core@magentocommerce.com>
 */
class Enterprise_Staging_Block_Adminhtml_Staging_Edit_Tabs_Settings extends Mage_Adminhtml_Block_Widget_Form
{
    /**
     * Keep main translate helper instance
     *
     * @var object
     */
    protected $helper;

    /**
     * Constructor
     */
    protected function _construct()
    {
        parent::_construct();
        $this->setFieldNameSuffix('');
    }

    /**
     * Prepare block additional children
     *
     * @return Enterprise_Staging_Block_Manage_Staging_Edit_Tabs_Settings
     */
    protected function _prepareLayout()
    {
        $this->setChild('continue_button',
            $this->getLayout()->createBlock('Mage_Adminhtml_Block_Widget_Button')
                ->setData(array(
                    'label'     => Mage::helper('Enterprise_Staging_Helper_Data')->__('Continue'),
                    'onclick'   => "setSettings('".$this->getContinueUrl()."', 'master_website_id', 'type')",
                    'class'     => 'save'
            ))
        );
        return parent::_prepareLayout();
    }

    /**
     * Prepare form before rendering HTML
     *
     * @return Enterprise_Staging_Block_Manage_Staging_Edit_Tabs_Settings
     */
    protected function _prepareForm()
    {
        $form = new Varien_Data_Form();

        $fieldset = $form->addFieldset('settings', array('legend'=>Mage::helper('Enterprise_Staging_Helper_Data')->__('Staging Website Settings')));

        $websiteCollection = Mage::getModel('Mage_Core_Model_Website')->getCollection()
            ->initCache($this->getCache(), 'app', array(Mage_Core_Model_Website::CACHE_TAG));

        $fieldset->addField('master_website_id', 'select', array(
            'label' => Mage::helper('Enterprise_Staging_Helper_Data')->__('Source Website'),
            'title' => Mage::helper('Enterprise_Staging_Helper_Data')->__('Source Website'),
            'name'  => 'master_website_id',
            'value' => '',
            'values'=> $websiteCollection->toOptionArray()
        ));

        $fieldset->addField('type', 'hidden', array(
            'label' => Mage::helper('Enterprise_Staging_Helper_Data')->__('Staging Type'),
            'title' => Mage::helper('Enterprise_Staging_Helper_Data')->__('Staging Type'),
            'name'  => 'type',
            'value' => 'website'
        ));

        $fieldset->addField('continue_button', 'note', array(
            'text'  => $this->getChildHtml('continue_button'),
        ));

        $form->setFieldNameSuffix($this->getFieldNameSuffix());
        $this->setForm($form);

        return parent::_prepareForm();
    }

    /**
     * Retrieve Url for continue button
     *
     * @return string
     */
    public function getContinueUrl()
    {
        return $this->getUrl('*/*/edit', array(
            '_current' => true,
            'master_website_id'  => '{{master_website_id}}',
            'type' => '{{type}}'
        ));
    }
}
