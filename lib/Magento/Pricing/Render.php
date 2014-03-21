<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Pricing
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Pricing;

use Magento\Pricing\Object\SaleableInterface;
use Magento\Pricing\Render\Layout;
use Magento\View\Element\Template;

class Render extends Template
{
    protected $defaultTypeRender = 'default';

    /**
     * @var Layout
     */
    protected $priceLayout;

    /**
     * @param Template\Context $context
     * @param Layout $priceLayout
     * @param array $data
     */
    public function __construct(
        Template\Context $context,
        Layout $priceLayout,
        array $data = array()
    ) {
        $this->priceLayout = $priceLayout;
        parent::__construct($context, $data);
    }

    /**
     * @return $this
     */
    protected function _prepareLayout()
    {
        $this->priceLayout->addHandle($this->getPriceRenderHandle());
        $this->priceLayout->loadLayout();
        return parent::_prepareLayout();
    }

    /**
     * @param string $priceType
     * @param SaleableInterface $product
     * @param array $arguments
     * @return string
     */
    public function render($priceType, SaleableInterface $product, array $arguments = [])
    {
        $useArguments = array_replace($this->_data, $arguments);

        // obtain concrete Price Render from "Pricing Layout Object"
        $priceRender = $this->getPriceBoxRender($product->getTypeId(), $priceType);
        if ($priceRender) {
            $result = $priceRender->render($priceType, $product, $useArguments);
        } else {
            $result = '';
        }
        // return rendered output
        return $result;
    }

    /**
     * @param string $objectType
     * @param string $priceType
     * @return \Magento\Pricing\Render\PriceBoxRenderInterface
     */
    protected function getPriceBoxRender($objectType, $priceType)
    {
        $priceRender = false;
        $renderList = $this->priceLayout->getBlock('price.render.prices');
        if ($renderList) {
            $priceRender = $renderList->getChildBlock($objectType . '.' . $priceType);
            if (!$priceRender) {
                $priceRender = $renderList->getChildBlock('default.' . $priceType);
                if (!$priceRender) {
                    $priceRender = $renderList->getChildBlock('default.default');
                }
            }
        }
        return $priceRender;
    }
}
