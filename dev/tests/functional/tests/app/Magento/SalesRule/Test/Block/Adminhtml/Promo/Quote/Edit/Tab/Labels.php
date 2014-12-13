<?php
/**
 * @copyright Copyright (c) 2014 X.commerce, Inc. (http://www.magentocommerce.com)
 */
namespace Magento\SalesRule\Test\Block\Adminhtml\Promo\Quote\Edit\Tab;

use Magento\Backend\Test\Block\Widget\Tab;
use Mtf\Client\Element;

/**
 * Class Labels
 * Backend sales rule label tab
 */
class Labels extends Tab
{
    /**
     * Store label field name
     */
    const STORE_LABEL_NAME = '[name="store_labels[%s]"]';

    /**
     * Fill data to labels fields on labels tab
     *
     * @param array $fields
     * @param Element|null $element
     * @return $this
     */
    public function fillFormTab(array $fields, Element $element = null)
    {
        if (isset($fields['store_labels'])) {
            $count = 0;
            foreach ($fields['store_labels']['value'] as $storeLabel) {
                $element->find(sprintf(self::STORE_LABEL_NAME, $count))->setValue($storeLabel);
                ++$count;
            }
        }

        return $this;
    }

    /**
     * Get data of labels tab
     *
     * @param array|null $fields
     * @param Element|null $element
     * @return array
     */
    public function getDataFormTab($fields = null, Element $element = null)
    {
        $storeLabels = [];
        $count = 0;
        $field = $this->_rootElement->find(sprintf(self::STORE_LABEL_NAME, $count));
        while ($field->isVisible()) {
            $fieldValue = $field->getValue();
            if ($fieldValue != '') {
                $storeLabels[$count] = $fieldValue;
            }
            ++$count;
            $field = $this->_rootElement->find(sprintf(self::STORE_LABEL_NAME, $count));
        }

        return ['store_labels' => $storeLabels];
    }
}
