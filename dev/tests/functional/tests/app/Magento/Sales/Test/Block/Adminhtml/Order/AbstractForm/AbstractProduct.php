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
class AbstractProduct extends Form
{
    /**
     * Fill item product data
     *
     * @param array $data
     * @return void
     */
    public function fillProduct(array $data)
    {
        $data = $this->dataMapping($this->prepareData($data));
        $this->_fill($data);
    }

    /**
     * Prepare data
     *
     * @param array $data
     * @return array
     */
    protected function prepareData(array $data)
    {
        $result = [];
        foreach ($data as $key => $value) {
            if ($value !== '-') {
                $result[$key] = $value;
            }
        }

        return $result;
    }
}
