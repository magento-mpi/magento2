<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Logging
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Difference columns renderer
 *
 */
namespace Magento\Logging\Block\Adminhtml\Details\Renderer;

class Diff
    extends \Magento\Adminhtml\Block\Widget\Grid\Column\Renderer\AbstractRenderer
{
    /**
     * Render the grid cell value
     *
     * @param \Magento\Object $row
     * @return string
     */
    public function render(\Magento\Object $row)
    {
        $html = '-';
        $columnData = $row->getData($this->getColumn()->getIndex());
        $specialFlag = false;
        try {
            $dataArray = unserialize($columnData);
            if (is_bool($dataArray)) {
                $html = $dataArray ? 'true' : 'false';
            }
            elseif (is_array($dataArray)) {
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
                    $html = '<dl>';
                    foreach ($dataArray as $key => $value) {
                        $html .= '<dt>' . $key . '</dt><dd>' . $this->escapeHtml($value) . '</dd>';
                    }
                    $html .= '</dl>';
                }
            } else {
                $html = $columnData;
            }
        }catch (\Exception $e) {
            $html = $columnData;
        }
        return $html;
    }
}
