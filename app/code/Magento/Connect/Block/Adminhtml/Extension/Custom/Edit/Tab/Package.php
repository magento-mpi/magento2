<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Connect
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Class block for package
 *
 * @category    Magento
 * @package     Magento_Connect
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Magento_Connect_Block_Adminhtml_Extension_Custom_Edit_Tab_Package
    extends Magento_Connect_Block_Adminhtml_Extension_Custom_Edit_Tab_Abstract
{
    /**
     * Prepare Package Info Form before rendering HTML
     *
     * @return Magento_Connect_Block_Adminhtml_Extension_Custom_Edit_Tab_Package
     */
    protected function _prepareForm()
    {
        parent::_prepareForm();

        $form = new Magento_Data_Form();
        $form->setHtmlIdPrefix('_package');

        $fieldset = $form->addFieldset('package_fieldset', array(
            'legend'    => Mage::helper('Magento_Connect_Helper_Data')->__('Package')
        ));

        if ($this->getData('name') != $this->getData('file_name')) {
            $this->setData('file_name_disabled', $this->getData('file_name'));
            $fieldset->addField('file_name_disabled', 'text', array(
                'name'      => 'file_name_disabled',
                'label'     => Mage::helper('Magento_Connect_Helper_Data')->__('Package File Name'),
                'disabled'  => 'disabled',
            ));
        }

        $fieldset->addField('file_name', 'hidden', array(
            'name'      => 'file_name',
        ));

        $fieldset->addField('name', 'text', array(
            'name'      => 'name',
            'label'     => Mage::helper('Magento_Connect_Helper_Data')->__('Name'),
            'required'  => true,
        ));

        $fieldset->addField('channel', 'text', array(
            'name'      => 'channel',
            'label'     => Mage::helper('Magento_Connect_Helper_Data')->__('Channel'),
            'required'  => true,
        ));

        $versionsInfo = array(
            array(
                'label' => Mage::helper('Magento_Connect_Helper_Data')->__('1.5.0.0 & later'),
                'value' => Magento_Connect_Package::PACKAGE_VERSION_2X
            ),
            array(
                'label' => Mage::helper('Magento_Connect_Helper_Data')->__('Pre-1.5.0.0'),
                'value' => Magento_Connect_Package::PACKAGE_VERSION_1X
            )
        );
        $fieldset->addField('version_ids','multiselect',array(
                'name'     => 'version_ids',
                'required' => true,
                'label'    => Mage::helper('Magento_Connect_Helper_Data')->__('Supported releases'),
                'style'    => 'height: 45px;',
                'values'   => $versionsInfo
        ));

        $fieldset->addField('summary', 'textarea', array(
            'name'      => 'summary',
            'label'     => Mage::helper('Magento_Connect_Helper_Data')->__('Summary'),
            'style'     => 'height:50px;',
            'required'  => true,
        ));

        $fieldset->addField('description', 'textarea', array(
            'name'      => 'description',
            'label'     => Mage::helper('Magento_Connect_Helper_Data')->__('Description'),
            'style'     => 'height:200px;',
            'required'  => true,
        ));

        $fieldset->addField('license', 'text', array(
            'name'      => 'license',
            'label'     => Mage::helper('Magento_Connect_Helper_Data')->__('License'),
            'required'  => true,
            'value'     => 'Open Software License (OSL 3.0)',
        ));

        $fieldset->addField('license_uri', 'text', array(
            'name'      => 'license_uri',
            'label'     => Mage::helper('Magento_Connect_Helper_Data')->__('License URI'),
            'value'     => 'http://opensource.org/licenses/osl-3.0.php',
        ));

        $form->setValues($this->getData());
        $this->setForm($form);

        return $this;
    }

    /**
     * Get Tab Label
     *
     * @return string
     */
    public function getTabLabel()
    {
        return Mage::helper('Magento_Connect_Helper_Data')->__('Package Info');
    }

    /**
     * Get Tab Title
     *
     * @return string
     */
    public function getTabTitle()
    {
        return Mage::helper('Magento_Connect_Helper_Data')->__('Package Info');
    }
}
