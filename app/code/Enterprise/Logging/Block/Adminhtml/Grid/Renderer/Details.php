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
class Enterprise_Logging_Block_Adminhtml_Grid_Renderer_Details
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
        try {
            $dataArray = unserialize($columnData);
            if (is_bool($dataArray)) {
                $html = $dataArray ? 'true' : 'false';
            }
            elseif (is_array($dataArray)) {
                if (isset($dataArray['general'])) {
                    if (!is_array($dataArray['general'])) {
                        $dataArray['general'] = array($dataArray['general']);
                    }
                    $html = $this->escapeHtml(implode(', ', $dataArray['general']));
                }
                /**
                 *  [additional] => Array
                 *          (
                 *               [Magento_Sales_Model_Order] => Array
                 *                  (
                 *                      [68] => Array
                 *                          (
                 *                              [increment_id] => 100000108,
                 *                              [grand_total] => 422.01
                 *                          )
                 *                      [94] => Array
                 *                          (
                 *                              [increment_id] => 100000121,
                 *                              [grand_total] => 492.77
                 *                          )
                 *
                 *                  )
                 *
                 *          )
                 */
                if (isset($dataArray['additional'])) {
                    $html .= '<br /><br />';
                    foreach ($dataArray['additional'] as $modelName => $modelsData) {
                        foreach ($modelsData as $mdoelId => $data) {
                            $html .= $this->escapeHtml(implode(', ', $data));
                        }
                    }
                }
            } else {
                $html = $columnData;
            }
        } catch (Exception $e) {
            $html = $columnData;
        }
        return $html;
    }
}
