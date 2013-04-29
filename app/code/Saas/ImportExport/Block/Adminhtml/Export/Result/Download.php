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
     * Initialize "controller"
     */
    protected function _construct()
    {
        $this->_addButton('download_export', array(
            'label'    => $this->__('Download'),
            'class'    => 'download',
            'onclick'  => 'return setLocation("' . $this->getDownloadUrl() . '")',
        ));

        $this->_addButton('remove_export', array(
            'label'    => $this->__('Remove'),
            'class'    => 'remove',
            'onclick'  => 'return setLocation("' . $this->getRemoveUrl() . '")',
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
     * @return string
     */
    public function getFileName()
    {
        return $this->_getData('file_name');
    }
}
