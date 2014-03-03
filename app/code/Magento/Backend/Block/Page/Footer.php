<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Backend
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Backend\Block\Page;

/**
 * Adminhtml footer block
 *
 * @category   Magento
 * @package    Magento_Backend
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Footer extends \Magento\Backend\Block\Template
{
    /**
     * @var string
     */
    protected $_template = 'page/footer.phtml';

    /**
     * @return void
     */
    protected function _construct()
    {
        $this->setShowProfiler(true);
    }
}
