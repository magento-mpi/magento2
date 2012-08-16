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
 * Staging entities tab
 *
 * @author     Magento Core Team <core@magentocommerce.com>
 */
class Enterprise_Staging_Block_Adminhtml_Staging_Edit_Tabs_Item extends Mage_Adminhtml_Block_Widget_Form
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
        $this->setFieldNameSuffix('staging[items]');
    }

    /**
     * Prepare form before rendering HTML
     *
     * @return Enterprise_Staging_Block_Manage_Staging_Edit_Tabs_Item
     */
    protected function _prepareForm()
    {
        $form          = new Varien_Data_Form();

        $staging       = $this->getStaging();
        $collection    = $staging->getItemsCollection();

        $fieldset = $form->addFieldset('staging_dataset_item',
            array('legend' => Mage::helper('Enterprise_Staging_Helper_Data')->__('Select Items to be Merged')));

        $extendInfo = $this->getExtendInfo();

        foreach (Mage::getSingleton('Enterprise_Staging_Model_Staging_Config')->getStagingItems() as $stagingItem) {
            if ((int)$stagingItem->is_backend) {
                continue;
            }

            $_code      = (string) $stagingItem->getName();
            $disabled   = "none";
            $note       = "";

            //process extend information
            if (!empty($extendInfo) && is_array($extendInfo)) {
                if ($extendInfo[$_code]["disabled"]==true) {
                    $disabled = "disabled";
                    $note = '<div style="color:#900">'.$extendInfo[$_code]["note"] . "<div>";
                } else {
                    $note = '<div style="color:#090">'.$extendInfo[$_code]["note"] . "<div>";
                }
            }

            $fieldset->addField('staging_item_code_'.$_code, 'checkbox',
                array(
                    'label'    => Mage::helper('Enterprise_Staging_Helper_Data')->__((string)$stagingItem->label),
                    'name'     => "{$_code}[staging_item_code]",
                    'value'    => $_code,
                    'checked'  => true,
                    $disabled  => true,
                    'note'     => $note,
                )
            );
        }

        $form->setFieldNameSuffix($this->getFieldNameSuffix());
        $this->setForm($form);

        return parent::_prepareForm();
    }

    /**
     * Retrive current staging object
     *
     * @return Enterprise_Staging_Model_Staging
     */
    public function getStaging()
    {
        if (!($this->getData('staging') instanceof Enterprise_Staging_Model_Staging)) {
            $this->setData('staging', Mage::registry('staging'));
        }
        return $this->getData('staging');
    }
}
