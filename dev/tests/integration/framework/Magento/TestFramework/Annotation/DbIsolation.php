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
class Magento_TestFramework_Annotation_DbIsolation
{
    /**
     * @var bool
     */
    protected $_isIsolationActive = false;

    /**
     * Handler for 'startTestTransactionRequest' event
     *
     * @param PHPUnit_Framework_TestCase $test
     * @param Magento_TestFramework_Event_Param_Transaction $param
     */
    public function startTestTransactionRequest(
        PHPUnit_Framework_TestCase $test, Magento_TestFramework_Event_Param_Transaction $param
    ) {
        $methodIsolation = $this->_getIsolation('method', $test);
        if ($this->_isIsolationActive) {
            if ($methodIsolation === false) {
                $param->requestTransactionRollback();
            }
        } else if ($methodIsolation || ($methodIsolation === null && $this->_getIsolation('class', $test))) {
            $param->requestTransactionStart();
        }
    }

    /**
     * Handler for 'endTestTransactionRequest' event
     *
     * @param PHPUnit_Framework_TestCase $test
     * @param Magento_TestFramework_Event_Param_Transaction $param
     */
    public function endTestTransactionRequest(
        PHPUnit_Framework_TestCase $test, Magento_TestFramework_Event_Param_Transaction $param
    ) {
        if ($this->_isIsolationActive && $this->_getIsolation('method', $test)) {
            $param->requestTransactionRollback();
        }
    }

    /**
     * Handler for 'startTransaction' event
     *
     * @param PHPUnit_Framework_TestCase $test
     *
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    public function startTransaction(PHPUnit_Framework_TestCase $test)
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
     * Retrieve database isolation annotation value for the current scope.
     * Possible results:
     *   NULL  - annotation is not defined
     *   TRUE  - annotation is defined as 'enabled'
     *   FALSE - annotation is defined as 'disabled'
     *
     * @param string $scope 'class' or 'method'
     * @param PHPUnit_Framework_TestCase $test
     * @return bool|null Returns NULL, if isolation is not defined for the current scope
     * @throws \Magento\MagentoException
     */
    protected function _getIsolation($scope, PHPUnit_Framework_TestCase $test)
    {
        $annotations = $test->getAnnotations();
        if (isset($annotations[$scope]['magentoDbIsolation'])) {
            $isolation = $annotations[$scope]['magentoDbIsolation'];
            if ($isolation !== array('enabled') && $isolation !== array('disabled')) {
                throw new \Magento\MagentoException(
                    'Invalid "@magentoDbIsolation" annotation, can be "enabled" or "disabled" only.'
                );
            }
            return ($isolation === array('enabled'));
        }
        return null;
    }
}
