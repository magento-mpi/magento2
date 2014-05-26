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

class PropertiesTab extends Tab {

    protected $propertiesTab = '#add_new_option_button';
    /**
     * Fill 'Attribute Manage Options' tab
     *
     * @param array $fields
     * @param Element|null $element
     * @return $this
     */
    public function fillFormTab(array $fields, Element $element = null)
    {
        $data = $this->dataMapping(['frontend_label'=>$fields['frontend_label'], 'frontend_input'=>$fields['frontend_input'], 'is_required'=>$fields['is_required'] ]);
        $this->_fill($data, $element);
        if($fields['frontend_input']['value'] == 'Dropdown' || $fields['frontend_input']['value'] == 'Multiple Select'){
            $type = 'Dropdown';
            foreach($fields['options']['value'] as $key => $field)
            {
                $this->_rootElement->find($this->propertiesTab)->click();
                $this->blockFactory->create(
                    'Magento\Catalog\Test\Block\Adminhtml\Product\Attribute\Edit\Options\Option' . $type,
                    ['element' => $this->_rootElement->find('.ui-sortable tr:nth-child(1)')]
                )->fillOptions($field);
            }
        }
        return $this;
    }
}
