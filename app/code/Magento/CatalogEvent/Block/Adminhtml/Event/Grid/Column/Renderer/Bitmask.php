<?php
/**
 * @copyright Copyright (c) 2014 X.commerce, Inc. (http://www.magentocommerce.com)
 */

/**
 * Catalog Events grid bitmask renderer
 *
 */
namespace Magento\CatalogEvent\Block\Adminhtml\Event\Grid\Column\Renderer;

use Magento\Backend\Block\Widget\Grid\Column\Renderer\Text;

class Bitmask extends Text
{
    /**
     * @param \Magento\Framework\Object $row
     * @return string
     */
    public function render(\Magento\Framework\Object $row)
    {
        $value = (int)$row->getData($this->getColumn()->getIndex());
        $result = [];
        foreach ($this->getColumn()->getOptions() as $option) {
            if (($value & $option['value']) == $option['value']) {
                $result[] = $option['label'];
            }
        }

        return $this->escapeHtml(implode(', ', $result));
    }
}
