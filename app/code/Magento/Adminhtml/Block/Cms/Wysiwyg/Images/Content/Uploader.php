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
 * Uploader block for Wysiwyg Images
 *
 * @category   Magento
 * @package    Magento_Adminhtml
 * @author     Magento Core Team <core@magentocommerce.com>
 */
namespace Magento\Adminhtml\Block\Cms\Wysiwyg\Images\Content;

class Uploader extends \Magento\Adminhtml\Block\Media\Uploader
{
    protected function _construct()
    {
        parent::_construct();
        $params = $this->getConfig()->getParams();
        $type = $this->_getMediaType();
        $allowed = \Mage::getSingleton('Magento\Cms\Model\Wysiwyg\Images\Storage')->getAllowedExtensions($type);
        $labels = array();
        $files = array();
        foreach ($allowed as $ext) {
            $labels[] = '.' . $ext;
            $files[] = '*.' . $ext;
        }
        $this->getConfig()
            ->setUrl(
                \Mage::getModel('Magento\Backend\Model\Url')
                    ->addSessionParam()
                    ->getUrl('*/*/upload', array('type' => $type)
                )
            )->setParams($params)
            ->setFileField('image')
            ->setFilters(array(
                'images' => array(
                    'label' => __('Images (%1)', implode(', ', $labels)),
                    'files' => $files
                )
            ));
    }

    /**
     * Return current media type based on request or data
     * @return string
     */
    protected function _getMediaType()
    {
        if ($this->hasData('media_type')) {
            return $this->_getData('media_type');
        }
        return $this->getRequest()->getParam('type');
    }
}
