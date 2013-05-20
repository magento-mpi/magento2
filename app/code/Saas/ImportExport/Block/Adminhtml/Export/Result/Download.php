<?php
/**
 * Export Download Block class
 *
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
class Saas_ImportExport_Block_Adminhtml_Export_Result_Download extends Mage_Backend_Block_Widget_Container
{
    /**
     * @var Saas_ImportExport_Helper_Export_File
     */
    protected $_fileHelper;

    /**
     * @param Mage_Core_Block_Template_Context $context
     * @param Saas_ImportExport_Helper_Export_File $fileHelper
     * @param array $data
     */
    public function __construct(
        Mage_Core_Block_Template_Context $context,
        Saas_ImportExport_Helper_Export_File $fileHelper,
        array $data = array()
    ) {
        parent::__construct($context, $data);
        $this->_fileHelper = $fileHelper;
    }

    /**
     * Initialize "controller"
     */
    protected function _construct()
    {
        $this->setTemplate('Saas_ImportExport::export/result/download.phtml');

        $this->_addButton('download_export', array(
            'label' => $this->__('Download'),
            'class' => 'download',
            'onclick' => 'return setLocation("' . $this->getDownloadUrl() . '")',
        ));

        $this->_addButton('remove_export', array(
            'label' => $this->__('Remove'),
            'class' => 'remove',
            'onclick' => 'return setLocation("' . $this->getRemoveUrl() . '")',
        ));

        parent::_construct();
    }

    /**
     * Get url for download export file
     *
     * @return string
     */
    public function getDownloadUrl()
    {
        return $this->getUrl('*/*/download');
    }

    /**
     * Get url for remove export file
     *
     * @return string
     */
    public function getRemoveUrl()
    {
        return $this->getUrl('*/*/remove');
    }

    /**
     * Get export file name
     *
     * @return string
     */
    public function getFileName()
    {
        return $this->isFileReady() ? $this->_fileHelper->getDownloadName() : '';
    }

    /**
     * Is export file ready
     *
     * @return bool
     */
    public function isFileReady()
    {
        return $this->_fileHelper->isExist();
    }
}
