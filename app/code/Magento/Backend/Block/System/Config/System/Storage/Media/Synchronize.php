<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Backend
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Synchronize button renderer
 */
class Magento_Backend_Block_System_Config_System_Storage_Media_Synchronize
    extends Magento_Backend_Block_System_Config_Form_Field
{
    protected $_template = 'system/config/system/storage/media/synchronize.phtml';

    /**
     * @var Magento_Core_Model_File_Storage
     */
    protected $_fileStorage;

    /**
     * @param Magento_Core_Helper_Data $coreData
     * @param Magento_Backend_Block_Template_Context $context
     * @param Magento_Core_Model_App $application
     * @param Magento_Core_Model_File_Storage $fileStorage
     * @param array $data
     */
    public function __construct(
        Magento_Core_Helper_Data $coreData,
        Magento_Backend_Block_Template_Context $context,
        Magento_Core_Model_App $application,
        Magento_Core_Model_File_Storage $fileStorage,
        array $data = array()
    ) {
        $this->_fileStorage = $fileStorage;
        parent::__construct($coreData, $context, $application, $data);
    }

    /**
     * Remove scope label
     *
     * @param  Magento_Data_Form_Element_Abstract $element
     * @return string
     */
    public function render(Magento_Data_Form_Element_Abstract $element)
    {
        $element->unsScope()->unsCanUseWebsiteValue()->unsCanUseDefaultValue();
        return parent::render($element);
    }

    /**
     * Return element html
     *
     * @param  Magento_Data_Form_Element_Abstract $element
     * @return string
     */
    protected function _getElementHtml(Magento_Data_Form_Element_Abstract $element)
    {
        return $this->_toHtml();
    }

    /**
     * Return ajax url for synchronize button
     *
     * @return string
     */
    public function getAjaxSyncUrl()
    {
        return $this->getUrl('*/system_config_system_storage/synchronize');
    }

    /**
     * Return ajax url for synchronize button
     *
     * @return string
     */
    public function getAjaxStatusUpdateUrl()
    {
        return $this->getUrl('*/system_config_system_storage/status');
    }

    /**
     * Generate synchronize button html
     *
     * @return string
     */
    public function getButtonHtml()
    {
        $button = $this->getLayout()->createBlock('Magento_Backend_Block_Widget_Button')
            ->setData(array(
                'id'        => 'synchronize_button',
                'label'     => __('Synchronize'),
                'onclick'   => 'javascript:synchronize(); return false;'
            ));

        return $button->toHtml();
    }

    /**
     * Retrieve last sync params settings
     *
     * Return array format:
     * array (
     *  => storage_type     int,
     *  => connection_name  string
     * )
     *
     * @return array
     */
    public function getSyncStorageParams()
    {
        $flag = $this->_fileStorage->getSyncFlag();
        $flagData = $flag->getFlagData();

        if ($flag->getState() == Magento_Core_Model_File_Storage_Flag::STATE_NOTIFIED
                && is_array($flagData)
            && isset($flagData['destination_storage_type']) && $flagData['destination_storage_type'] != ''
            && isset($flagData['destination_connection_name'])
        ) {
            $storageType    = $flagData['destination_storage_type'];
            $connectionName = $flagData['destination_connection_name'];
        } else {
            $storageType    = Magento_Core_Model_File_Storage::STORAGE_MEDIA_FILE_SYSTEM;
            $connectionName = '';
        }

        return array(
            'storage_type'      => $storageType,
            'connection_name'   => $connectionName
        );
    }
}
