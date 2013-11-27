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
 * Adminhtml footer block
 *
 * @category   Magento
 * @package    Magento_Backend
 * @author      Magento Core Team <core@magentocommerce.com>
 */
namespace Magento\Backend\Block\Page;

class Footer extends \Magento\Backend\Block\Template
{
    protected $_template = 'page/footer.phtml';

    protected function _construct()
    {
        $this->setShowProfiler(true);
    }
}
