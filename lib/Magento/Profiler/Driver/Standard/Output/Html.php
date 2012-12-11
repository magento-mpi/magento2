<?php
/**
 * Class that represents profiler output in HTML format
 *
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
class Magento_Profiler_Driver_Standard_Output_Html extends Magento_Profiler_Driver_Standard_OutputAbstract
{
    /**
     * Display profiling results
     *
     * @param Magento_Profiler_Driver_Standard_Stat $stat
     */
    public function display(Magento_Profiler_Driver_Standard_Stat $stat)
    {
        $out = array();
        $out[] = '<table border="1" cellspacing="0" cellpadding="2">';
        $out[] = '<caption>' . $this->_renderCaption() . '</caption>';
        $out[] = '<tr>';
        foreach (array_keys($this->_columns) as $columnLabel) {
            $out[] = '<th>' . $columnLabel . '</th>';
        }
        $out[] = '</tr>';
        foreach ($this->_getTimerIds($stat) as $timerId) {
            $out[] = '<tr>';
            foreach ($this->_columns as $column) {
                $out[] = '<td title="' . $timerId . '">'
                    . $this->_renderColumnValue($stat->fetch($timerId, $column), $column)
                    . '</td>';
            }
            $out[] = '</tr>';
        }
        $out[] = '</table>';
        $out[] = '';
        $out = implode("\n", $out);
        echo $out;
    }

    /**
     * Render timer id column value
     *
     * @param string $timerId
     * @return string
     */
    protected function _renderTimerId($timerId)
    {
        $nestingSep = preg_quote(Magento_Profiler::NESTING_SEPARATOR, '/');
        return preg_replace('/.+?' . $nestingSep . '/', '&middot;&nbsp;&nbsp;', $timerId);
    }
}
