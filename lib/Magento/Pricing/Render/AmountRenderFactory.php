<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Pricing\Render;

use Magento\Pricing\Price\PriceInterface;

/**
 * Amount Renderer Factory
 */
class AmountRenderFactory
{
    /**
     * Default amount renderer
     */
    const AMOUNT_RENDERER_DEFAULT = 'Magento\Pricing\Render\Amount';

    /**
     * Price amount renderer list
     *
     * @var array
     */
    protected $types = [];

    /**
     * Create amount renderer object for particular product
     *
     * @param \Magento\View\LayoutInterface $layout
     * @param string $amountRender
     * @param string $template
     * @param array $arguments
     * @throws \InvalidArgumentException
     * @return AmountRenderInterface
     */
    public function create(
        \Magento\View\LayoutInterface $layout,
        $amountRender = self::AMOUNT_RENDERER_DEFAULT,
        $template = '',
        array $arguments = []
    ) {
        if (!isset($arguments['template'])) {
            $arguments['template'] = $template;
        }

        $amountBlock = $layout->createBlock($amountRender, '', $arguments);

        if (!$amountBlock instanceof AmountRenderInterface) {
            throw new \InvalidArgumentException(
                $amountRender . ' doesn\'t implement \Magento\Pricing\AmountRenderInterface'
            );
        }

        return $amountBlock;
    }
}
