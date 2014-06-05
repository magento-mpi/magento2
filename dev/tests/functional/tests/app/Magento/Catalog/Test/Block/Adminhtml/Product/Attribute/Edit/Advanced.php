<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Catalog\Test\Block\Adminhtml\Product\Attribute\Edit;

use Mtf\Client\Element;
use Magento\Backend\Test\Block\Widget\Tab;

/**
 * Class AdvancedPropertiesTab
 * Tab "Advanced Attribute Properties"
 */
class Advanced extends Tab
{
    /**
     * "Advanced Attribute Properties" tab-button
     *
     * @var string
     */
    protected $propertiesTab = '[data-target="#advanced_fieldset-content"][data-toggle="collapse"]';

    /**
     * "Advanced Attribute Properties" tab-button active
     *
     * @var string
     */
    protected $propertiesTabActive = '.title.active';

    /**
     * Fill 'Advanced Attribute Properties' tab
     *
     * @param array $fields
     * @param Element|null $element
     * @return $this
     */
    public function fillFormTab(array $fields, Element $element = null)
    {
        if (!$this->_rootElement->find($this->propertiesTabActive)->isVisible()) {
            $this->_rootElement->find($this->propertiesTab)->click();
        }

        return parent::fillFormTab($fields);
    }
}
