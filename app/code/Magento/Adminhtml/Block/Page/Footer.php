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
 * Adminhtml footer block
 *
 * @category   Magento
 * @package    Magento_Adminhtml
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Magento_Adminhtml_Block_Page_Footer extends Magento_Adminhtml_Block_Template
{
    protected $_template = 'page/footer.phtml';

    protected function _construct()
    {
        $this->setShowProfiler(true);
    }
}
