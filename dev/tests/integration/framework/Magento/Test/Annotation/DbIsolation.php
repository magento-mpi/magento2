<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento
 * @subpackage  integration_tests
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Implementation of the @magentoDbIsolation DocBlock annotation
 */
class Magento_Test_Annotation_DbIsolation
{
    /**
     * @var bool
     */
    protected $_isIsolationActive = false;

    /**
     * Handler for 'startTestTransactionRequest' event
     *
     * @param PHPUnit_Framework_TestCase $test
     * @param Magento_Test_Event_Param_Transaction $param
     */
    public function startTestTransactionRequest(
        PHPUnit_Framework_TestCase $test, Magento_Test_Event_Param_Transaction $param
    ) {
        $isMethodIsolated = $this->_isIsolationEnabled('method', $test);
        if ($isMethodIsolated || (!$this->_isIsolationActive && $this->_isIsolationEnabled('class', $test))) {
            $param->requestTransactionBegin();
        }
    }

    /**
     * Handler for 'endTestTransactionRequest' event
     *
     * @param PHPUnit_Framework_TestCase $test
     * @param Magento_Test_Event_Param_Transaction $param
     */
    public function endTestTransactionRequest(
        PHPUnit_Framework_TestCase $test, Magento_Test_Event_Param_Transaction $param
    ) {
        if ($this->_isIsolationActive && $this->_isIsolationEnabled('method', $test)) {
            $param->requestTransactionRollback();
        }
    }

    /**
     * Handler for 'beginTransaction' event
     *
     * @param PHPUnit_Framework_TestCase $test
     *
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    public function beginTransaction(PHPUnit_Framework_TestCase $test)
    {
        $this->_isIsolationActive = true;
    }

    /**
     * Handler for 'rollbackTransaction' event
     */
    public function rollbackTransaction()
    {
        $this->_isIsolationActive = false;
    }

    /**
     * Whether database isolation is enabled for the current test or not
     *
     * @param string $scope 'class' or 'method'
     * @param PHPUnit_Framework_TestCase $test
     * @return bool
     * @throws Magento_Exception
     */
    protected function _isIsolationEnabled($scope, PHPUnit_Framework_TestCase $test)
    {
        $annotations = $test->getAnnotations();
        if (isset($annotations[$scope]['magentoDbIsolation'])) {
            $isolation = $annotations[$scope]['magentoDbIsolation'];
            if ($isolation !== array('enabled') && $isolation !== array('disabled')) {
                throw new Magento_Exception(
                    'Invalid "@magentoDbIsolation" annotation, can be "enabled" or "disabled" only.'
                );
            }
            return ($isolation === array('enabled'));
        }
        return false;
    }
}
