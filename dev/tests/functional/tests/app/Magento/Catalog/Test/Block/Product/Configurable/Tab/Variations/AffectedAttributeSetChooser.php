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

namespace Magento\Catalog\Test\Block\Product\Configurable\Tab\Variations;

use Mtf\Block\Block;
use Mtf\Client\Element;
use Mtf\Factory\Factory;
use Mtf\Client\Element\Locator;

/**
 * Class Variations
 * Configurable variations
 *
 * @package Magento\Catalog\Test\Block\Product\Configurable\Tab\Variations
 */
class AffectedAttributeSetChooser extends Block
{
    /**
     * @param $attributeSetName
     */
    public function chooseNewAndConfirm($attributeSetName)
    {
        $this->_rootElement->find('#affected-attribute-set-new-container input')->click();
        $this->_rootElement->find('#affected-attribute-set-new-name-container input')->setValue($attributeSetName);
        $this->_rootElement->find('.ui-dialog-buttonset button:nth-child(2)')->click();
    }

}
