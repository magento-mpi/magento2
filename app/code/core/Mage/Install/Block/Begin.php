<?php
/**
 * {license_notice}
 *
 * @category    Mage
 * @package     Mage_Install
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Installation begin block
 *
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Mage_Install_Block_Begin extends Mage_Install_Block_Abstract
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
        return file_get_contents(BP . DS . (string)Mage::getConfig()->getNode('install/eula_file'));
    }
}
