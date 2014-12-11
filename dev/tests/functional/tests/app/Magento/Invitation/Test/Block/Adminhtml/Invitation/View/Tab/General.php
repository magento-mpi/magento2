<?php
/**
 * @copyright Copyright (c) 2014 X.commerce, Inc. (http://www.magentocommerce.com)
 */

namespace Magento\Invitation\Test\Block\Adminhtml\Invitation\View\Tab;

use Magento\Backend\Test\Block\Widget\Tab;
use Mtf\Client\Element;

/**
 * Tab for general invitation information.
 */
class General extends Tab
{
    /**
     * Get data of tab
     *
     * @param array|null $fields
     * @param Element|null $element
     * @return array
     */
    public function getDataFormTab($fields = null, Element $element = null)
    {
        $data = $this->dataMapping($fields);
        $dataFields = [];
        $context = ($element === null) ? $this->_rootElement : $element;
        foreach ($data as $key => $field) {
            $element = $this->getElement($context, $field);
            if ($this->mappingMode || $element->isVisible()) {
                $dataFields[$key] = $element->getText();
            }
        }

        return $dataFields;
    }
}
