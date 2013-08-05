<?php
/**
 * {license_notice}
 *
 * @category    Mage
 * @package     Mage_Connect
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Class block for package
 *
 * @category    Mage
 * @package     Mage_Connect
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Mage_Connect_Block_Adminhtml_Extension_Custom_Edit_Tab_Package
    extends Mage_Connect_Block_Adminhtml_Extension_Custom_Edit_Tab_Abstract
{
    /**
     * Prepare Package Info Form before rendering HTML
     *
     * @return Mage_Connect_Block_Adminhtml_Extension_Custom_Edit_Tab_Package
     */
    protected function _prepareForm()
    {
        parent::_prepareForm();

        $form = new Varien_Data_Form();
        $form->setHtmlIdPrefix('_package');

        $fieldset = $form->addFieldset('package_fieldset', array(
            'legend'    => __('Package')
        ));

        if ($this->getData('name') != $this->getData('file_name')) {
            $this->setData('file_name_disabled', $this->getData('file_name'));
            $fieldset->addField('file_name_disabled', 'text', array(
                'name'      => 'file_name_disabled',
                'label'     => __('Package File Name'),
                'disabled'  => 'disabled',
            ));
        }

        $fieldset->addField('file_name', 'hidden', array(
            'name'      => 'file_name',
        ));

        $fieldset->addField('name', 'text', array(
            'name'      => 'name',
            'label'     => __('Name'),
            'required'  => true,
        ));

        $fieldset->addField('channel', 'text', array(
            'name'      => 'channel',
            'label'     => __('Channel'),
            'required'  => true,
        ));

        $versionsInfo = array(
            array(
                'label' => __('1.5.0.0 & later'),
                'value' => Mage_Connect_Package::PACKAGE_VERSION_2X
            ),
            array(
                'label' => __('Pre-1.5.0.0'),
                'value' => Mage_Connect_Package::PACKAGE_VERSION_1X
            )
        );
        $fieldset->addField('version_ids','multiselect',array(
                'name'     => 'version_ids',
                'required' => true,
                'label'    => __('Supported releases'),
                'style'    => 'height: 45px;',
                'values'   => $versionsInfo
        ));

        $fieldset->addField('summary', 'textarea', array(
            'name'      => 'summary',
            'label'     => __('Summary'),
            'style'     => 'height:50px;',
            'required'  => true,
        ));

        $fieldset->addField('description', 'textarea', array(
            'name'      => 'description',
            'label'     => __('Description'),
            'style'     => 'height:200px;',
            'required'  => true,
        ));

        $fieldset->addField('license', 'text', array(
            'name'      => 'license',
            'label'     => __('License'),
            'required'  => true,
            'value'     => 'Open Software License (OSL 3.0)',
        ));

        $fieldset->addField('license_uri', 'text', array(
            'name'      => 'license_uri',
            'label'     => __('License URI'),
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
        return __('Package Info');
    }

    /**
     * Get Tab Title
     *
     * @return string
     */
    public function getTabTitle()
    {
        return __('Package Info');
    }
}
