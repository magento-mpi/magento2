<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Backend\Block\System\Config\System\Storage\Media;

/**
 * Synchronize button renderer
 */
class Synchronize extends \Magento\Backend\Block\System\Config\Form\Field
{
    /**
     * @var string
     */
    protected $_template = 'Magento_Backend::system/config/system/storage/media/synchronize.phtml';

    /**
     * @var \Magento\Core\Model\File\Storage
     */
    protected $_fileStorage;

    /**
     * @param \Magento\Backend\Block\Template\Context $context
     * @param \Magento\Core\Model\File\Storage $fileStorage
     * @param array $data
     */
    public function __construct(
        \Magento\Backend\Block\Template\Context $context,
        \Magento\Core\Model\File\Storage $fileStorage,
        array $data = array()
    ) {
        $this->_fileStorage = $fileStorage;
        parent::__construct($context, $data);
    }

    /**
     * Remove scope label
     *
     * @param  \Magento\Framework\Data\Form\Element\AbstractElement $element
     * @return string
     */
    public function render(\Magento\Framework\Data\Form\Element\AbstractElement $element)
    {
        $element->unsScope()->unsCanUseWebsiteValue()->unsCanUseDefaultValue();
        return parent::render($element);
    }

    /**
     * Return element html
     *
     * @param  \Magento\Framework\Data\Form\Element\AbstractElement $element
     * @return string
     */
    protected function _getElementHtml(\Magento\Framework\Data\Form\Element\AbstractElement $element)
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
        $button = $this->getLayout()->createBlock(
            'Magento\Backend\Block\Widget\Button'
        )->setData(
            array(
                'id' => 'synchronize_button',
                'label' => __('Synchronize'),
                'onclick' => 'javascript:synchronize(); return false;'
            )
        );

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

        if ($flag->getState() == \Magento\Core\Model\File\Storage\Flag::STATE_NOTIFIED && is_array(
            $flagData
        ) && isset(
            $flagData['destination_storage_type']
        ) && $flagData['destination_storage_type'] != '' && isset(
            $flagData['destination_connection_name']
        )
        ) {
            $storageType = $flagData['destination_storage_type'];
            $connectionName = $flagData['destination_connection_name'];
        } else {
            $storageType = \Magento\Core\Model\File\Storage::STORAGE_MEDIA_FILE_SYSTEM;
            $connectionName = '';
        }

        return array('storage_type' => $storageType, 'connection_name' => $connectionName);
    }
}
