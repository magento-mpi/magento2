<?php
/**
 * Composite price modifier can be used.
 * Any module can add its price modifier to extend price modification from other modules.
 *
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Catalog\Model\Product\PriceModifier;

use Magento\Catalog\Model\Product\PriceModifierInterface;
use Magento\Catalog\Model\Product;
use Magento\Framework\ObjectManager;

class Composite implements PriceModifierInterface
{
    /**
     * @var \Magento\Framework\ObjectManager
     */
    protected $objectManager;

    /**
     * @var array
     */
    protected $modifiers;

    /**
     * @param ObjectManager $objectManager
     * @param array $modifiers
     */
    public function __construct(ObjectManager $objectManager, array $modifiers = array())
    {
        $this->objectManager = $objectManager;
        $this->modifiers = $modifiers;
    }

    /**
     * Modify price
     *
     * @param mixed $price
     * @param Product $product
     * @return mixed
     */
    public function modifyPrice($price, Product $product)
    {
        foreach ($this->modifiers as $modifierClass) {
            $price = $this->objectManager->get($modifierClass)->modifyPrice($price, $product);
        }
        return $price;
    }
}
