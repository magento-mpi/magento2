<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Catalog
 * @copyright   {copyright}
 * @license     {license_link}
 */


/**
 * Catalog Config Field Select Flat Product Block
 *
 * @category   Magento
 * @package    Magento_Catalog
 * @author     Magento Core Team <core@magentocommerce.com>
 */
namespace Magento\Catalog\Block\Adminhtml\System\Config\Form\Field\Select;

class Flatproduct
    extends \Magento\Backend\Block\System\Config\Form\Field
{
    /**
     * @var \Magento\Catalog\Helper\Product\Flat
     */
    protected $_flatProduct;

    /**
     * @param \Magento\Backend\Block\Template\Context $context
     * @param array $data
     */
    public function __construct(
        \Magento\Backend\Block\Template\Context $context,
        array $data = array()
    ) {
        parent::__construct($context, $data);
    }
}
