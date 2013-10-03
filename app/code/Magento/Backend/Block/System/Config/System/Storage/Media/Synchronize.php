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
namespace Magento\Backend\Block\System\Config\System\Storage\Media;

class Synchronize
    extends \Magento\Backend\Block\System\Config\Form\Field
{
    protected $_template = 'system/config/system/storage/media/synchronize.phtml';

    /**
     * @var \Magento\Core\Model\File\Storage
     */
    protected $_fileStorage;

    /**
     * @param \Magento\Core\Helper\Data $coreData
     * @param \Magento\Backend\Block\Template\Context $context
     * @param \Magento\Core\Model\App $application
     * @param \Magento\Core\Model\File\Storage $fileStorage
     * @param array $data
     */
    public function __construct(
        \Magento\Core\Helper\Data $coreData,
        \Magento\Backend\Block\Template\Context $context,
        \Magento\Core\Model\App $application,
        \Magento\Core\Model\File\Storage $fileStorage,
        array $data = array()
    ) {
        $this->_fileStorage = $fileStorage;
        parent::__construct($coreData, $context, $application, $data);
    }

    /**
     * Remove scope label
     *
     * @param  \Magento\Data\Form\Element\AbstractElement $element
     * @return string
     */
    public function render(\Magento\Data\Form\Element\AbstractElement $element)
    {
        $element->unsScope()->unsCanUseWebsiteValue()->unsCanUseDefaultValue();
        return parent::render($element);
    }

    /**
     * Return element html
     *
     * @param  \Magento\Data\Form\Element\AbstractElement $element
     * @return string
     */
    protected function _getElementHtml(\Magento\Data\Form\Element\AbstractElement $element)
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
        $button = $this->getLayout()->createBlock('Magento\Backend\Block\Widget\Button')
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

        if ($flag->getState() == \Magento\Core\Model\File\Storage\Flag::STATE_NOTIFIED
                && is_array($flagData)
            && isset($flagData['destination_storage_type']) && $flagData['destination_storage_type'] != ''
            && isset($flagData['destination_connection_name'])
        ) {
            $storageType    = $flagData['destination_storage_type'];
            $connectionName = $flagData['destination_connection_name'];
        } else {
            $storageType    = \Magento\Core\Model\File\Storage::STORAGE_MEDIA_FILE_SYSTEM;
            $connectionName = '';
        }

        return array(
            'storage_type'      => $storageType,
            'connection_name'   => $connectionName
        );
    }
}
