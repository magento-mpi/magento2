<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Adminhtml
 * @copyright   {copyright}
 * @license     {license_link}
 */


/**
 * Media library js helper
 *
 * @deprecated since 1.7.0.0
 * @category   Magento
 * @package    Magento_Adminhtml
 * @author     Magento Core Team <core@magentocommerce.com>
 */
class Magento_Adminhtml_Helper_Media_Js extends Magento_Core_Helper_Js
{
    /**
     * @inheritdoc
     */
    protected function _populateTranslateData()
    {
        $this->_addTranslation('Complete', __('Complete'));
        $this->_addTranslation('The file size should be more than 0 bytes.', __('The file size should be more than 0 bytes.'));
        $this->_addTranslation('Upload Security Error', __('Upload Security Error'));
        $this->_addTranslation('Upload HTTP Error'    , __('Upload HTTP Error'));
        $this->_addTranslation('Upload I/O Error'    , __('Upload I/O Error'));
        $this->_addTranslation('SSL Error: Invalid or self-signed certificate', __('SSL Error: Invalid or self-signed certificate'));
        $this->_addTranslation('Tb', __('Tb'));
        $this->_addTranslation('Gb', __('Gb'));
        $this->_addTranslation('Mb', __('Mb'));
        $this->_addTranslation('Kb', __('Kb'));
        $this->_addTranslation('b', __('b'));
    }
}
