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

use Magento\Backend\Block\System\Config\Form\Field;
use Magento\Data\Form\Element\AbstractElement;

class Flatproduct extends Field
{
    /**
     * @var \Magento\Catalog\Helper\Product\Flat
     */
    protected $_flatProduct;

    /**
     * @param \Magento\Backend\Block\Template\Context $context
     * @param \Magento\Catalog\Helper\Product\Flat $flatProduct
     * @param array $data
     */
    public function __construct(
        \Magento\Backend\Block\Template\Context $context,
        \Magento\Catalog\Helper\Product\Flat $flatProduct,
        array $data = array()
    ) {
        $this->_flatProduct = $flatProduct;
        parent::__construct($context, $data);
    }

    /**
     * Retrieve Element HTML
     *
     * @param AbstractElement $element
     * @return string
     */
    protected function _getElementHtml(AbstractElement $element) {
        if (!$this->_flatProduct->isBuilt()) {
            $element->setDisabled(true)
                ->setValue(0);
        }
        return parent::_getElementHtml($element);
    }

}
