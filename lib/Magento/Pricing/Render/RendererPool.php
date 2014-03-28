<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Pricing\Render;

use Magento\Pricing\Amount\AmountInterface;
use Magento\Pricing\Object\SaleableInterface;
use Magento\Pricing\Price\PriceInterface;
use Magento\View\Element\AbstractBlock;

/**
 * Render Factory
 */
class RendererPool extends AbstractBlock
{
    /**
     * Default price group type
     */
    const DEFAULT_PRICE_GROUP_TYPE = 'default';

    /**
     * Default price renderer
     */
    const PRICE_RENDERER_DEFAULT = 'Magento\Pricing\Render\PriceBox';

    /**
     * Default amount renderer
     */
    const AMOUNT_RENDERER_DEFAULT = 'Magento\Pricing\Render\Amount';

    /**
     * Create amount renderer
     *
     * @param string $priceCode
     * @param SaleableInterface $saleableItem
     * @param array $data
     * @throws \InvalidArgumentException
     * @return PriceBoxRenderInterface
     */
    public function createPriceRender(
        $priceCode,
        SaleableInterface $saleableItem,
        array $data = []
    ) {
        $type = $saleableItem->getTypeId();

        // implement class resolving fallback
        $pattern = [
            $type . '/prices/' . $priceCode . '/render_class',
            $type . '/default_render_class',
            'default/prices/' . $priceCode . '/render_class',
            'default/default_render_class'
        ];
        $renderClassName = $this->findDataByPattern($pattern);
        if (!$renderClassName) {
            throw new \InvalidArgumentException(
                $priceCode . ' isn\'t registered price type'
            );
        }

        $price = $saleableItem->getPriceInfo()->getPrice($priceCode);
        if (!$price) {
            throw new \InvalidArgumentException(
                $priceCode . ' is not registered Price Type'
            );
        }

        $arguments['data'] = $data;
        $arguments['rendererPool'] = $this;
        $arguments['price'] = $price;
        $arguments['saleableItem'] = $saleableItem;

        $renderBlock = $this->getLayout()->createBlock($renderClassName, '', $arguments);
        if (!$renderBlock instanceof PriceBoxRenderInterface) {
            throw new \InvalidArgumentException(
                $renderBlock . ' doesn\'t implement \Magento\Pricing\Render\PriceBoxRenderInterface'
            );
        }
        $renderBlock->setTemplate($this->getRenderBlockTemplate($type, $priceCode));
        return $renderBlock;
    }

    /**
     * Create amount renderer
     *
     * @param AmountInterface $amount
     * @param SaleableInterface $saleableItem
     * @param PriceInterface $price
     * @param array $data
     * @return AmountRenderInterface
     * @throws \InvalidArgumentException
     */
    public function createAmountRender(
        AmountInterface $amount,
        SaleableInterface $saleableItem = null,
        PriceInterface $price = null,
        array $data = []
    ) {
        if ($saleableItem === null) {
            $type = self::DEFAULT_PRICE_GROUP_TYPE;
        } else {
            $type = $saleableItem->getTypeId();
        }

        if (!$price) {
            $priceCode = null;
            $renderClassName = self::AMOUNT_RENDERER_DEFAULT;
        } else {
            $priceCode = $price->getPriceType();
            // implement class resolving fallback
            $pattern = [
                $type . '/prices/' . $priceCode . '/amount_render_class',
                $type . '/default_amount_render_class',
                'default/prices/' . $priceCode . '/amount_render_class',
                'default/default_amount_render_class'
            ];
            $renderClassName = $this->findDataByPattern($pattern);
            if (!$renderClassName) {
                throw new \InvalidArgumentException(
                    "There is no amount render class registered for '{$priceCode}' price type"
                );
            }
        }

        $arguments['data'] = $data;
        $arguments['rendererPool'] = $this;
        $arguments['amount'] = $amount;

        if ($saleableItem) {
            $arguments['saleableItem'] = $saleableItem;
            if ($price) {
                $arguments['price'] = $price;
            }
        }

        $amountBlock = $this->getLayout()->createBlock($renderClassName, '', $arguments);
        if (!$amountBlock instanceof AmountRenderInterface) {
            throw new \InvalidArgumentException(
                $renderClassName . ' doesn\'t implement \Magento\Pricing\Render\AmountRenderInterface'
            );
        }
        $amountBlock->setTemplate($this->getAmountRenderBlockTemplate($type, $priceCode));
        return $amountBlock;
    }

    /**
     * @return array
     */
    public function getAdjustmentRenders()
    {
        $renders = [];
        foreach ($this->getData('default/adjustments') as $code => $configuration) {
            $render = $this->getLayout()->createBlock($configuration['adjustment_render_class']);
            $render->setTemplate($configuration['adjustment_render_template']);
            $renders[$code] = $render;
        }
        return $renders;
    }

    /**
     * @param string $type
     * @param string $priceCode
     * @return string
     * @throws \InvalidArgumentException
     */
    protected function getAmountRenderBlockTemplate($type, $priceCode)
    {
        $pattern = [
            $type . '/prices/' . $priceCode . '/amount_render_template',
            $type . '/default_amount_render_template',
            'default/prices/' . $priceCode . '/amount_render_template',
            'default/default_amount_render_template'
        ];
        $template = $this->findDataByPattern($pattern);
        if (!$template) {
            throw new \InvalidArgumentException(
                $type . ' amount render block isn\'t configured properly'
            );
        }
        return $template;
    }

    /**
     * @param string $type
     * @param string $priceCode
     * @return string
     * @throws \InvalidArgumentException
     */
    protected function getRenderBlockTemplate($type, $priceCode)
    {
        $pattern = [
            $type . '/prices/' . $priceCode . '/render_template',
            $type . '/default_render_template',
            'default/prices/' . $priceCode . '/render_template',
            'default/default_render_template'
        ];
        $template = $this->findDataByPattern($pattern);
        if (!$template) {
            throw new \InvalidArgumentException(
                $priceCode . ' render block isn\'t configured properly'
            );
        }
        return $template;
    }

    /**
     * @param array $pattern
     * @return null|string
     */
    protected function findDataByPattern($pattern)
    {
        $data = null;
        foreach ($pattern as $key) {
            $data = $this->getData($key);
            if ($data) {
                break;
            }
        }
        return $data;
    }
}
