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
use Magento\View\Element\AbstractBlock;

/**
 * Base price render
 *
 * @method string getPriceRenderHandle()
 */
class Render extends AbstractBlock
{
    /**
     * Default type renderer
     *
     * @var string
     */
    protected $defaultTypeRender = 'default';

    /**
     * Price layout
     *
     * @var Layout
     */
    protected $priceLayout;

    /**
     * Constructor
     *
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
     * Prepare layout
     *
     * @return $this
     */
    protected function _prepareLayout()
    {
        $this->priceLayout->addHandle($this->getPriceRenderHandle());
        $this->priceLayout->loadLayout();
        return parent::_prepareLayout();
    }

    /**
     * Render price
     *
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
     * Obtain price box renderer
     *
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
