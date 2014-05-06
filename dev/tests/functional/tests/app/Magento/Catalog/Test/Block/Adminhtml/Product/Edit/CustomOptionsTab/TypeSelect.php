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
namespace Magento\Catalog\Test\Block\Adminhtml\Product\Edit\CustomOptionsTab;

use Mtf\Client\Element;
use Mtf\Client\Element\Locator;
use Mtf\Block\Block;

/**
 * Select Type
 */
class TypeSelect extends Block
{
    public function fill($data)
    {
        $this->_rootElement->find('[name$="[title]"]')->setValue($data['title']);
        $this->_rootElement->find('[name$="[price]"]')->setValue($data['price']);
        $this->_rootElement->find('[name$="[price_type]"]', Locator::SELECTOR_CSS, 'select')
            ->setValue($data['price_type']);
        $this->_rootElement->find('[name$="[sku]"]')->setValue($data['sku']);
    }
}
