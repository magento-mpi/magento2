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
namespace Magento\Adminhtml\Block\Page;

class Footer extends \Magento\Adminhtml\Block\Template
{
    protected $_template = 'page/footer.phtml';

    protected function _construct()
    {
        $this->setShowProfiler(true);
    }
}
