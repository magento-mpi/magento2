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
 * Chooser Container for "Product Link" Cms Widget Plugin
 *
 * @category   Magento
 * @package    Magento_Catalog
 * @author     Magento Core Team <core@magentocommerce.com>
 */
namespace Magento\Catalog\Block\Adminhtml\Product\Widget\Chooser;

use Magento\Backend\Block\Template;

class Container extends Template
{
    /**
     * @var string
     */
    protected $_template = 'catalog/product/widget/chooser/container.phtml';
}
