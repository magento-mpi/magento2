<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Bundle\Test\Block\Catalog\Product\View\Type\Option;

use Magento\Bundle\Test\Block\Catalog\Product\View\Type\Option;

/**
 * Class Multiple
 * Bundle option Multiple type
 */
class Multiple extends Option
{
    /**
     * Set data in multiselect option
     *
     * @param array $data
     * @return void
     */
    public function fillOption(array $data)
    {
       $mapping = $this->dataMapping($data);
       $this->getElement($this->_rootElement, $mapping['name'])->setValue($mapping['name']['value'], false);
    }
}
