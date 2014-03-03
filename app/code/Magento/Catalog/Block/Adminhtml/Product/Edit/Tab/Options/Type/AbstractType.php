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
 * @package    Magento_Catalog
 * @author     Magento Core Team <core@magentocommerce.com>
 */

namespace Magento\Catalog\Block\Adminhtml\Product\Edit\Tab\Options\Type;

class AbstractType extends \Magento\Backend\Block\Widget
{
    /**
     * @var string
     */
    protected $_name = 'abstract';

    /**
     * @var \Magento\Catalog\Model\Config\Source\Product\Options\Price
     */
    protected $_optionPrice;

    /**
     * @param \Magento\Backend\Block\Template\Context $context
     * @param \Magento\Catalog\Model\Config\Source\Product\Options\Price $optionPrice
     * @param array $data
     */
    public function __construct(
        \Magento\Backend\Block\Template\Context $context,
        \Magento\Catalog\Model\Config\Source\Product\Options\Price $optionPrice,
        array $data = array()
    ) {
        $this->_optionPrice = $optionPrice;
        parent::__construct($context, $data);
    }

    /**
     * @return $this
     */
    protected function _prepareLayout()
    {
        $this->setChild('option_price_type',
            $this->getLayout()
                ->addBlock('Magento\View\Element\Html\Select',
                    $this->getNameInLayout() . '.option_price_type',
                    $this->getNameInLayout())
                ->setData(array(
                    'id' => 'product_option_${option_id}_price_type',
                    'class' => 'select product-option-price-type'
                ))
        );

        $this->getChildBlock('option_price_type')
            ->setName('product[options][${option_id}][price_type]')
            ->setOptions($this->_optionPrice->toOptionArray());

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
