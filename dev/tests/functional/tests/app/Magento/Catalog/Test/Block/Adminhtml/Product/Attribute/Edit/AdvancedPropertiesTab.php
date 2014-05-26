<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Catalog\Test\Block\Adminhtml\Product\Attribute\Edit;

use Mtf\ObjectManager;
use Mtf\Client\Element;
use Magento\Backend\Test\Block\Widget\Tab;

class AdvancedPropertiesTab extends Tab {

    protected $propertiesTab = '[data-toggle="collapse"]';
    /**
     * Fill 'Advanced Attribute Properties' tab
     *
     * @param array $fields
     * @param Element|null $element
     * @return $this
     */
    public function fillFormTab(array $fields, Element $element = null)
    {
        $this->_rootElement->find($this->propertiesTab)->click();
        parent::fillFormTab($fields);
        return $this;
    }
}
