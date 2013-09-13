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
 * customers defined options
 *
 * @category   Magento
 * @package    Magento_Adminhtml
 * @author     Magento Core Team <core@magentocommerce.com>
 */

namespace Magento\Adminhtml\Block\Catalog\Product\Edit\Tab\Options\Type;

class AbstractType extends \Magento\Adminhtml\Block\Widget
{
    protected $_name = 'abstract';

    protected function _prepareLayout()
    {
        $this->setChild('option_price_type',
            $this->getLayout()->addBlock('Magento\Adminhtml\Block\Html\Select', '', $this->getNameInLayout())
                ->setData(array(
                    'id' => 'product_option_${option_id}_price_type',
                    'class' => 'select product-option-price-type'
                ))
        );

        $this->getChildBlock('option_price_type')
            ->setName('product[options][${option_id}][price_type]')
            ->setOptions(\Mage::getSingleton('Magento\Catalog\Model\Config\Source\Product\Options\Price')->toOptionArray());

        return parent::_prepareLayout();
    }

    /**
     * Get html of Price Type select element
     *
     * @return string
     */
    public function getPriceTypeSelectHtml()
    {
        if ($this->getCanEditPrice() === false) {
            $this->getChildBlock('option_price_type')->setExtraParams('disabled="disabled"');
        }
        return $this->getChildHtml('option_price_type');
    }

}
