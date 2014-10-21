<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Sales\Test\Block\Adminhtml\Order\AbstractForm;

use Mtf\Block\Form;

/**
 * Class Product
 * Item product form on items block
 */
class Product extends Form
{
    /**
     * Fill item product data
     *
     * @param array $data
     * @return void
     */
    public function fillProduct(array $data)
    {
        $data = $this->dataMapping($data);
        $this->_fill($data);
    }
}
