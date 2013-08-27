<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Install
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Installation begin block
 */
class Magento_Install_Block_Begin extends Magento_Install_Block_Abstract
{
    protected $_template = 'begin.phtml';

    /**
     * Get wizard URL
     *
     * @return string
     */
    public function getPostUrl()
    {
        return Mage::getUrl('install/wizard/beginPost');
    }

    /**
     * Get License HTML contents
     *
     * @return string
     */
    public function getLicenseHtml()
    {
        return $this->_filesystem->read(BP . DS . (string)Mage::getConfig()->getNode('install/eula_file'));
    }
}
