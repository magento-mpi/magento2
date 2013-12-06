<?php
/**
 * {license_notice}
 *
 * @category    Mtf
 * @package     Mtf
 * @subpackage  functional_tests
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Checkout\Test\Block\Cart;

use Mtf\Block\Form;
use Mtf\Client\Element;

/**
 * Cart shipping block
 *
 * @package Magento\Checkout\Test\Block\Cart
 */
class Shipping extends Form
{
    /**
     * Form wrapper selector
     *
     * @var string
     */
    protected $formWrapper = '.content';

    /**
     * Open shipping form selector
     *
     * @var string
     */
    protected $openForm = '.title';

    /**
     * Get quote selector
     *
     * @var string
     */
    protected $getQuote = '.action.quote';

    /**
     * @var array
     */
    protected $_mapping = [
        'postcode' => [
            'selector' => '#postcode',
        ]
    ];

    /**
     * Open estimate shipping and tax form
     */
    public function openEstimateShippingAndTax()
    {
        if (!$this->_rootElement->find($this->formWrapper)->isVisible()) {
            $this->_rootElement->find($this->openForm)->click();
        }
    }

    /**
     * Get quote
     */
    public function getQuote()
    {
        $this->_rootElement->find($this->getQuote)->click();
    }

    /**
     * {@inheritdoc}
     */
    protected function _fill(array $fields, Element $element = null)
    {
        $formFields = ['country_id', 'region',  'postcode'];
        $fields = array_intersect_key($fields, array_flip($formFields));
        parent::_fill($fields, $element);
    }
}
