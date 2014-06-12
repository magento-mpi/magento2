<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Catalog\Test\Block\Adminhtml\Product\Attribute\Edit\Tab;

use Mtf\Client\Element;
use Magento\Backend\Test\Block\Widget\Tab;

/**
 * Class Options
 * Options form
 */
class Options extends Tab
{
    /**
     * 'Add Option' button
     *
     * @var string
     */
    protected $addOption = '#add_new_option_button';

    /**
     * Fill 'Options' tab
     *
     * @param array $fields
     * @param Element|null $element
     * @return $this
     */
    public function fillFormTab(array $fields, Element $element = null)
    {
        foreach ($fields['options']['value'] as $field) {
            $this->_rootElement->find($this->addOption)->click();
            $this->blockFactory->create(
                'Magento\Catalog\Test\Block\Adminhtml\Product\Attribute\Edit\Tab\Options\Option',
                ['element' => $this->_rootElement->find('.ui-sortable tr:nth-child(1)')]
            )->fillOptions($field);
        }
        return $this;
    }
}
