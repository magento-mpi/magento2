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
        foreach (array_keys($this->_getColumns()) as $columnLabel) {
            $out[] = '<th>' . $columnLabel . '</th>';
        }
        $out[] = '</tr>';
        foreach ($this->_getTimerNames($stat) as $timerName) {
            $out[] = '<tr>';
            foreach ($this->_getColumns() as $key) {
                $out[] = '<td title="' . $timerName . '">'
                    . $this->_renderColumnValue($stat->fetch($timerName, $key), $key)
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
     * Render timer name column value
     *
     * @param string $timerName
     * @return string
     */
    protected function _renderTimerName($timerName)
    {
        $nestingSep = preg_quote(Magento_Profiler::NESTING_SEPARATOR, '/');
        return preg_replace('/.+?' . $nestingSep . '/', '&middot;&nbsp;&nbsp;', $timerName);
    }
}
