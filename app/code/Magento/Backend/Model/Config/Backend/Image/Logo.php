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
 * System config Logo image field backend model
 */
namespace Magento\Backend\Model\Config\Backend\Image;

class Logo extends \Magento\Backend\Model\Config\Backend\Image
{
    /**
     * The tail part of directory path for uploading
     *
     */
    const UPLOAD_DIR = 'logo';

    /**
     * Return path to directory for upload file
     *
     * @return string
     * @throw \Magento\Core\Exception
     */
    protected function _getUploadDir()
    {
        return $this->_mediaDirectory->getAbsolutePath($this->_appendScopeInfo(self::UPLOAD_DIR));
    }

    /**
     * Makes a decision about whether to add info about the scope.
     *
     * @return boolean
     */
    protected function _addWhetherScopeInfo()
    {
        return true;
    }
}
