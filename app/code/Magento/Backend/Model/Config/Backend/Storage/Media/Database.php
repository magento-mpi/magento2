<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Backend
 * @copyright   {copyright}
 * @license     {license_link}
 */


namespace Magento\Backend\Model\Config\Backend\Storage\Media;

class Database extends \Magento\Core\Model\Config\Value
{
    /**
     * Create db structure
     *
     * @return \Magento\Backend\Model\Config\Backend\Storage\Media\Database
     */
    protected function _afterSave()
    {
        $helper = \Mage::helper('Magento\Core\Helper\File\Storage');
        $helper->getStorageModel(null, array('init' => true));

        return $this;
    }
}
