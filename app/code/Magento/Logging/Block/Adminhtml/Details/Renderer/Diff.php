<?php
/**
 * @copyright Copyright (c) 2014 X.commerce, Inc. (http://www.magentocommerce.com)
 */

/**
 * Difference columns renderer
 *
 */
namespace Magento\Logging\Block\Adminhtml\Details\Renderer;

class Diff extends \Magento\Backend\Block\Widget\Grid\Column\Renderer\AbstractRenderer
{
    /**
     * Render the grid cell value
     *
     * @param \Magento\Framework\Object $row
     * @return string
     */
    public function render(\Magento\Framework\Object $row)
    {
        $html = '-';
        $columnData = $row->getData($this->getColumn()->getIndex());
        $specialFlag = false;
        try {
            $dataArray = unserialize($columnData);
            if (is_bool($dataArray)) {
                $html = $dataArray ? 'true' : 'false';
            } elseif (is_array($dataArray)) {
                if (isset($dataArray['__no_changes'])) {
                    $html = __('No changes');
                    $specialFlag = true;
                }
                if (isset($dataArray['__was_deleted'])) {
                    $html = __('Item was deleted');
                    $specialFlag = true;
                }
                if (isset($dataArray['__was_created'])) {
                    $html = __('N/A');
                    $specialFlag = true;
                }
                $dataArray = (array)$dataArray;
                if (!$specialFlag) {
                    $html = '<dl class="list-parameters">';
                    foreach ($dataArray as $key => $value) {
                        $html .= '<dt class="parameter">' . $key . '</dt>';
                        if (!is_array($value)) {
                            $html .= '<dd class="value">' . $this->escapeHtml(
                                $value
                            ) . '</dd>';
                        } elseif ($key == 'time') {
                            $html .= '<dd class="value">' . $this->escapeHtml(
                                implode(":", $value)
                            ) . '</dd>';
                        } else {
                            foreach ($value as $arrayValue) {
                                $html .= '<dd class="value">' . $this->escapeHtml(
                                    $arrayValue
                                ) . '</dd>';
                            }
                        }
                    }
                    $html .= '</dl>';
                }
            } else {
                $html = $columnData;
            }
        } catch (\Exception $e) {
            $html = $columnData;
        }
        return $html;
    }
}
