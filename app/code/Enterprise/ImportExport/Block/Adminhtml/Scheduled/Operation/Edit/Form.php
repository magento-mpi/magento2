<?php
/**
 * {license_notice}
 *
 * @category    Enterprise
 * @package     Enterprise_ImportExport
 * @copyright   {copyright}
 * @license     {license_link}
 */

// @codingStandardsIgnoreStart
/**
 * Scheduled operation create/edit form
 *
 * @category    Enterprise
 * @package     Enterprise_ImportExport
 * @author      Magento Core Team <core@magentocommerce.com>
 *
 * @method string getGeneralSettingsLabel() getGeneralSettingsLabel()
 * @method string getFileSettingsLabel() getFileSettingsLabel()
 * @method string getEmailSettingsLabel() getEmailSettingsLabel()
 * @method Enterprise_ImportExport_Block_Adminhtml_Scheduled_Operation_Edit_Form setGeneralSettingsLabel() setGeneralSettingsLabel(string $value)
 * @method Enterprise_ImportExport_Block_Adminhtml_Scheduled_Operation_Edit_Form setFileSettingsLabel() setFileSettingsLabel(string $value)
 * @method Enterprise_ImportExport_Block_Adminhtml_Scheduled_Operation_Edit_Form setEmailSettingsLabel() setEmailSettingsLabel(string $value)
 */
// @codingStandardsIgnoreEnd
abstract class Enterprise_ImportExport_Block_Adminhtml_Scheduled_Operation_Edit_Form
    extends Magento_Adminhtml_Block_Widget_Form
{
    /**
     * Prepare general form for scheduled operation
     *
     * @return Enterprise_ImportExport_Block_Adminhtml_Scheduled_Operation_Edit_Form
     */
    protected function _prepareForm()
    {
        /** @var $operation Enterprise_ImportExport_Model_Scheduled_Operation */
        $operation = Mage::registry('current_operation');
        $form = new Magento_Data_Form(array(
            'id'     => 'edit_form',
            'name'   => 'scheduled_operation'
        ));
        // settings information
        $this->_addGeneralSettings($form, $operation);

        // file information
        $this->_addFileSettings($form, $operation);

        // email notifications
        $this->_addEmailSettings($form, $operation);

        $form->setMethod('post');
        $form->setUseContainer(true);
        $form->setAction($this->getUrl('*/*/save'));

        $this->setForm($form);
        if (is_array($operation->getStartTime())) {
            $operation->setStartTime(join(',', $operation->getStartTime()));
        }
        $operation->setStartTime(str_replace(':', ',', $operation->getStartTime()));

        return $this;
    }

    /**
     * Add general information fieldset to form
     *
     * @param Magento_Data_Form $form
     * @param Enterprise_ImportExport_Model_Scheduled_Operation $operation
     * @return Enterprise_ImportExport_Block_Adminhtml_Scheduled_Operation_Edit_Form
     */
    protected function _addGeneralSettings($form, $operation)
    {
        $fieldset = $form->addFieldset('operation_settings', array(
            'legend' => $this->getGeneralSettingsLabel()
        ));

        if ($operation->getId()) {
            $fieldset->addField('id', 'hidden', array(
                'name'      => 'id',
                'required'  => true
            ));
        }
        $fieldset->addField('operation_type', 'hidden', array(
            'name'     => 'operation_type',
            'required' => true
        ));

        $fieldset->addField('name', 'text', array(
            'name'      => 'name',
            'title'     => Mage::helper('Enterprise_ImportExport_Helper_Data')->__('Name'),
            'label'     => Mage::helper('Enterprise_ImportExport_Helper_Data')->__('Name'),
            'required'  => true
        ));

        $fieldset->addField('details', 'textarea', array(
            'name'      => 'details',
            'title'     => Mage::helper('Enterprise_ImportExport_Helper_Data')->__('Description'),
            'label'     => Mage::helper('Enterprise_ImportExport_Helper_Data')->__('Description'),
            'required'  => false
        ));

        $entities = Mage::getModel(
            'Magento_ImportExport_Model_Source_' . uc_words($operation->getOperationType()) . '_Entity'
        )->toOptionArray();

        $fieldset->addField('entity', 'select', array(
            'name'      => 'entity_type',
            'title'     => Mage::helper('Enterprise_ImportExport_Helper_Data')->__('Entity Type'),
            'label'     => Mage::helper('Enterprise_ImportExport_Helper_Data')->__('Entity Type'),
            'required'  => true,
            'values'    => $entities
        ));

        $fieldset->addField('start_time', 'time', array(
            'name'      => 'start_time',
            'title'     => Mage::helper('Enterprise_ImportExport_Helper_Data')->__('Start Time'),
            'label'     => Mage::helper('Enterprise_ImportExport_Helper_Data')->__('Start Time'),
            'required'  => true,
        ));

        /** @var $operationData Enterprise_ImportExport_Model_Scheduled_Operation_Data */
        $operationData = Mage::getSingleton('Enterprise_ImportExport_Model_Scheduled_Operation_Data');

        $fieldset->addField('freq', 'select', array(
            'name'      => 'freq',
            'title'     => Mage::helper('Enterprise_ImportExport_Helper_Data')->__('Frequency'),
            'label'     => Mage::helper('Enterprise_ImportExport_Helper_Data')->__('Frequency'),
            'required'  => true,
            'values'    => $operationData->getFrequencyOptionArray()
        ));

        $fieldset->addField('status', 'select', array(
            'name'      => 'status',
            'title'     => Mage::helper('Enterprise_ImportExport_Helper_Data')->__('Status'),
            'label'     => Mage::helper('Enterprise_ImportExport_Helper_Data')->__('Status'),
            'required'  => true,
            'values'    => $operationData->getStatusesOptionArray()
        ));

        return $this;
    }

    /**
     * Add file information fieldset to form
     *
     * @param Magento_Data_Form $form
     * @param Enterprise_ImportExport_Model_Scheduled_Operation $operation
     * @return Enterprise_ImportExport_Block_Adminhtml_Scheduled_Operation_Edit_Form
     */
    protected function _addFileSettings($form, $operation)
    {
        /** @var $operationData Enterprise_ImportExport_Model_Scheduled_Operation_Data */
        $operationData = Mage::getSingleton('Enterprise_ImportExport_Model_Scheduled_Operation_Data');

        $fieldset = $form->addFieldset('file_settings', array(
            'legend' => $this->getFileSettingsLabel()
        ));

        $fieldset->addField('server_type', 'select', array(
            'name'      => 'file_info[server_type]',
            'title'     => Mage::helper('Enterprise_ImportExport_Helper_Data')->__('Server Type'),
            'label'     => Mage::helper('Enterprise_ImportExport_Helper_Data')->__('Server Type'),
            'required'  => true,
            'values'    => $operationData->getServerTypesOptionArray(),
        ));

        $fieldset->addField('file_path', 'text', array(
            'name'      => 'file_info[file_path]',
            'title'     => Mage::helper('Enterprise_ImportExport_Helper_Data')->__('File Directory'),
            'label'     => Mage::helper('Enterprise_ImportExport_Helper_Data')->__('File Directory'),
            'required'  => true,
            'note'      => Mage::helper('Enterprise_ImportExport_Helper_Data')
                ->__('For Type "Local Server" use relative path to Magento installation, e.g. var/export, var/import, var/export/some/dir')
        ));

        $fieldset->addField('host', 'text', array(
            'name'      => 'file_info[host]',
            'title'     => Mage::helper('Enterprise_ImportExport_Helper_Data')->__('FTP Host[:Port]'),
            'label'     => Mage::helper('Enterprise_ImportExport_Helper_Data')->__('FTP Host[:Port]'),
            'class'     => 'ftp-server server-dependent'
        ));

        $fieldset->addField('user', 'text', array(
            'name'      => 'file_info[user]',
            'title'     => Mage::helper('Enterprise_ImportExport_Helper_Data')->__('User Name'),
            'label'     => Mage::helper('Enterprise_ImportExport_Helper_Data')->__('User Name'),
            'class'     => 'ftp-server server-dependent'
        ));

        $fieldset->addField('password', 'password', array(
            'name'      => 'file_info[password]',
            'title'     => Mage::helper('Enterprise_ImportExport_Helper_Data')->__('Password'),
            'label'     => Mage::helper('Enterprise_ImportExport_Helper_Data')->__('Password'),
            'class'     => 'ftp-server server-dependent'
        ));

        $fieldset->addField('file_mode', 'select', array(
            'name'      => 'file_info[file_mode]',
            'title'     => Mage::helper('Enterprise_ImportExport_Helper_Data')->__('File Mode'),
            'label'     => Mage::helper('Enterprise_ImportExport_Helper_Data')->__('File Mode'),
            'values'    => $operationData->getFileModesOptionArray(),
            'class'     => 'ftp-server server-dependent'
        ));

        /** @var $sourceYesNo Mage_Backend_Model_Config_Source_Yesno */
        $sourceYesNo = Mage::getSingleton('Mage_Backend_Model_Config_Source_Yesno');
        $fieldset->addField('passive', 'select', array(
            'name'      => 'file_info[passive]',
            'title'     => Mage::helper('Enterprise_ImportExport_Helper_Data')->__('Passive Mode'),
            'label'     => Mage::helper('Enterprise_ImportExport_Helper_Data')->__('Passive Mode'),
            'values'    => $sourceYesNo->toOptionArray(),
            'class'     => 'ftp-server server-dependent'
        ));

        return $this;
    }

    /**
     * Add file information fieldset to form
     *
     * @param Magento_Data_Form $form
     * @param Enterprise_ImportExport_Model_Scheduled_Operation $operation
     * @return Enterprise_ImportExport_Block_Adminhtml_Scheduled_Operation_Edit_Form
     */
    protected function _addEmailSettings($form, $operation)
    {
        $fieldset = $form->addFieldSet('email_settings', array(
            'legend' => $this->getEmailSettingsLabel()
        ));

        /** @var $sourceEmailIdentity Mage_Backend_Model_Config_Source_Email_Identity */
        $sourceEmailIdentity = Mage::getModel('Mage_Backend_Model_Config_Source_Email_Identity');

        $fieldset->addField('email_receiver', 'select', array(
            'name'      => 'email_receiver',
            'title'     => Mage::helper('Enterprise_ImportExport_Helper_Data')->__('Failed Email Receiver'),
            'label'     => Mage::helper('Enterprise_ImportExport_Helper_Data')->__('Failed Email Receiver'),
            'values'    => $sourceEmailIdentity->toOptionArray()
        ));

        $fieldset->addField('email_sender', 'select', array(
            'name'      => 'email_sender',
            'title'     => Mage::helper('Enterprise_ImportExport_Helper_Data')->__('Failed Email Sender'),
            'label'     => Mage::helper('Enterprise_ImportExport_Helper_Data')->__('Failed Email Sender'),
            'values'    => $sourceEmailIdentity->toOptionArray()
        ));

        $fieldset->addField('email_template', 'select', array(
            'name'      => 'email_template',
            'title'     => Mage::helper('Enterprise_ImportExport_Helper_Data')->__('Failed Email Template'),
            'label'     => Mage::helper('Enterprise_ImportExport_Helper_Data')->__('Failed Email Template')
        ));

        $fieldset->addField('email_copy', 'text', array(
            'name'      => 'email_copy',
            'title'     => Mage::helper('Enterprise_ImportExport_Helper_Data')->__('Send Failed Email Copy To'),
            'label'     => Mage::helper('Enterprise_ImportExport_Helper_Data')->__('Send Failed Email Copy To')
        ));

        /** @var $sourceEmailMethod Mage_Backend_Model_Config_Source_Email_Method */
        $sourceEmailMethod = Mage::getModel('Mage_Backend_Model_Config_Source_Email_Method');

        $fieldset->addField('email_copy_method', 'select', array(
            'name'      => 'email_copy_method',
            'title'     => Mage::helper('Enterprise_ImportExport_Helper_Data')->__('Send Failed Email Copy Method'),
            'label'     => Mage::helper('Enterprise_ImportExport_Helper_Data')->__('Send Failed Email Copy Method'),
            'values'    => $sourceEmailMethod->toOptionArray()
        ));

        return $this;
    }

    /**
     * Set values to form from operation model
     *
     * @param array $data
     * @return Enterprise_ImportExport_Block_Adminhtml_Scheduled_Operation_Edit_Form|bool
     */
    protected function _setFormValues(array $data)
    {
        if (!is_object($this->getForm())) {
            return false;
        }
        if (isset($data['file_info'])) {
            $fileInfo = $data['file_info'];
            unset($data['file_info']);
            if (is_array($fileInfo)) {
                $data = array_merge($data, $fileInfo);
            }
        }
        if (isset($data['entity_type'])) {
            $data['entity'] = $data['entity_type'];
        }
        $this->getForm()->setValues($data);
        return $this;
    }
}
