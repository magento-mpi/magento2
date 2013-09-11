<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_DesignEditor
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Files files block
 *
 * @method \Magento\Theme\Block\Adminhtml\Wysiwyg\Files\Content\Files
 *    setStorage(\Magento\Theme\Model\Wysiwyg\Storage $storage)
 * @method \Magento\Theme\Model\Wysiwyg\Storage getStorage
 */
namespace Magento\DesignEditor\Block\Adminhtml\Editor\Tools\Files\Content;

class Files
    extends \Magento\Theme\Block\Adminhtml\Wysiwyg\Files\Content\Files
{
    /**
     * @return string
     */
    public function getStorageType()
    {
        return __($this->helper('\Magento\Theme\Helper\Storage')->getStorageType());
    }

}
