<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Wishlist\Test\Block\Customer\Wishlist\Items;

use Mtf\Block\Form;

/**
 * Class Product
 * Wishlist item product form
 */
class Product extends Form
{
    /**
     * Selector for 'Add to Cart' button
     *
     * @var string
     */
    protected $addToCart = '.action.tocart';

    /**
     * Selector for 'Remove item' button
     *
     * @var string
     */
    protected $remove = '[data-role="remove"]';

    /**
     * Selector for 'View Details' element
     *
     * @var string
     */
    protected $viewDetails = '.details.tooltip';

    /**
     * Selector for 'Options Details' tooltip
     *
     * @var string
     */
    protected $optionsDetails = '.tooltip.content .values';

    /**
     * Fill item product details
     *
     * @param array $fields
     * @return void
     */
    public function fillProduct(array $fields)
    {
        $mapping = $this->dataMapping($fields);
        $this->_fill($mapping);
    }

    /**
     * Click button 'Add To Cart'
     *
     * @return void
     */
    public function clickAddToCart()
    {
        $this->_rootElement->find($this->addToCart)->click();
    }

    /**
     * Remove product from wish list
     *
     * @return void
     */
    public function remove()
    {
        $this->_rootElement->find($this->remove)->click();
    }

    /**
     * Get product options
     *
     * @return array|null
     */
    public function getOptions()
    {
        $viewDetails = $this->_rootElement->find($this->viewDetails);
        if ($viewDetails->isVisible()) {
            $viewDetails->click();
            $values = $this->_rootElement->find($this->optionsDetails)->getElements();
            $data = [];
            foreach ($values as $value) {
                $data[] = $value->getText();
            }
            return $data;
        } else {
            return null;
        }
    }
}
