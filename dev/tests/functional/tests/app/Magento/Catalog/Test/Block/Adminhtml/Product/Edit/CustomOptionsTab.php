<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Catalog\Test\Block\Adminhtml\Product\Edit;

use Mtf\Client\Element;
use Magento\Backend\Test\Block\Widget\Tab;
use Mtf\Factory\Factory;

/**
 * Custom Options Tab
 *
 */
class CustomOptionsTab extends Tab
{
    /**
     * Fill custom options
     *
     * @param array $fields
     * @param Element $element
     * @return $this
     */
    public function fillFormTab(array $fields, Element $element)
    {
        if (!isset($fields['custom_options'])) {
            return $this;
        }
        $root = $element;
        $this->_rootElement->waitUntil(
            function () use ($root) {
                return $root->find('#Custom_Options')->isVisible();
            }
        );

        $button = $root->find('[data-ui-id="admin-product-options-add-button"]');

        $container = $root->find('#product_options_container');

        if (isset($fields['custom_options']['value'])) {
            foreach ($fields['custom_options']['value'] as $index => $data) {
                $button->click();
                $row = $container->find('.fieldset-wrapper:nth-child(' . ($index + 1) . ')');
                Factory::getBlockFactory()
                    ->getMagentoCatalogAdminhtmlProductEditCustomOptionsTabOption($row)
                    ->fill($data);
            }
        }

        return $this;
    }
}
