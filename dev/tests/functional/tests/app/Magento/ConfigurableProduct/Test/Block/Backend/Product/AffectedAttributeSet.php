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

namespace Magento\ConfigurableProduct\Test\Block\Backend\Product;

use Mtf\Block\Block;
use Mtf\Client\Element;
use Mtf\Client\Element\Locator;
use Magento\ConfigurableProduct\Test\Fixture\CatalogProductConfigurable;

/**
 * Class AffectedAttributeSet
 * Choose affected attribute set dialog popup window
 */
class AffectedAttributeSet extends Block
{
    /**
     * Create new attribute set based on default
     *
     * @var string
     */
    protected $affectedAttributeSet = '[name=affected-attribute-set][value=new]';

    /**
     * New attribute set name
     *
     * @var string
     */
    protected $attributeSetName = '[name=new-attribute-set-name]';

    /**
     * 'Confirm' button
     *
     * @var string
     */
    protected $confirmButton = '[id*=confirm-button]';

    /**
     * Choose affected attribute set
     *
     * @param CatalogProductConfigurable $fixture
     */
    public function chooseAttributeSet(CatalogProductConfigurable $fixture)
    {
        $attributeSetName = $fixture->getAttributeSetName();
        if ($attributeSetName) {
            $this->_rootElement->find($this->affectedAttributeSet)->click();
            $this->_rootElement->find($this->attributeSetName)->setValue($attributeSetName);
        }
        $this->_rootElement->find($this->confirmButton)->click();
    }
}
