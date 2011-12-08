<?php
/**
 * {license_notice}
 *
 * @category   Mage
 * @package    Mage_LoadTest
 * @copyright  {copyright}
 * @license    {license_link}
 */

class Mage_LoadTest_Model_Db_Profiler extends Zend_Db_Profiler
{
    protected static $_traces = array();
    /**
     * Ends a query.  Pass it the handle that was returned by queryStart().
     * This will mark the query as ended and save the time.
     *
     * @param  integer $queryId
     * @throws Zend_Db_Profiler_Exception
     * @return void
     */
    public function queryEnd($queryId)
    {
        parent::queryEnd($queryId);
        // Don't do anything if the Zend_Db_Profiler is not enabled.
        if (!$this->_enabled) {
            return;
        }

        try {
            throw new Exception('Trace for qyery: '. $queryId);
        }
        catch (Exception $e) {
            self::$_traces[$queryId] = $e->__toString();
        }
    }

    public function getTrace($queryId)
    {
        return isset(self::$_traces[$queryId]) ? self::$_traces[$queryId] : false;
    }
}