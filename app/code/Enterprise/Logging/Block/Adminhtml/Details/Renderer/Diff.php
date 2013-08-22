<?php
/**
 * {license_notice}
 *
 * @category    Enterprise
 * @package     Enterprise_Logging
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Difference columns renderer
 *
 */
class Enterprise_Logging_Block_Adminhtml_Details_Renderer_Diff
    extends Magento_Adminhtml_Block_Widget_Grid_Column_Renderer_Abstract
{
    /**
     * Render the grid cell value
     *
     * @param Magento_Object $row
     * @return string
     */
    public function render(Magento_Object $row)
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
        }catch (Exception $e) {
            $html = $columnData;
        }
        return $html;
    }
}
