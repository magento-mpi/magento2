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
 * Implementation of the @magentoDataFixture DocBlock annotation
 */
class Magento_TestFramework_Annotation_DataFixture
{
    /**
     * @var string
     */
    protected $_fixtureBaseDir;

    /**
     * Fixtures that have been applied
     *
     * @var array
     */
    private $_appliedFixtures = array();

    /**
     * Constructor
     *
     * @param string $fixtureBaseDir
     * @throws Magento_Exception
     */
    public function __construct($fixtureBaseDir)
    {
        if (!is_dir($fixtureBaseDir)) {
            throw new Magento_Exception("Fixture base directory '$fixtureBaseDir' does not exist.");
        }
        $this->_fixtureBaseDir = realpath($fixtureBaseDir);
    }

    /**
     * Handler for 'startTestTransactionRequest' event
     *
     * @param PHPUnit_Framework_TestCase $test
     * @param Magento_TestFramework_Event_Param_Transaction $param
     */
    public function startTestTransactionRequest(
        PHPUnit_Framework_TestCase $test, Magento_TestFramework_Event_Param_Transaction $param
    ) {
        /* Start transaction before applying first fixture to be able to revert them all further */
        if ($this->_getFixtures('method', $test)) {
            /* Re-apply even the same fixtures to guarantee data consistency */
            if ($this->_appliedFixtures) {
                $param->requestTransactionRollback();
            }
            $param->requestTransactionStart();
        } else if (!$this->_appliedFixtures && $this->_getFixtures('class', $test)) {
            $param->requestTransactionStart();
        }
    }

    /**
     * Handler for 'endTestNeedTransactionRollback' event
     *
     * @param PHPUnit_Framework_TestCase $test
     * @param Magento_TestFramework_Event_Param_Transaction $param
     */
    public function endTestTransactionRequest(
        PHPUnit_Framework_TestCase $test, Magento_TestFramework_Event_Param_Transaction $param
    ) {
        /* Isolate other tests from test-specific fixtures */
        if ($this->_appliedFixtures && $this->_getFixtures('method', $test)) {
            $param->requestTransactionRollback();
        }
    }

    /**
     * Handler for 'startTransaction' event
     *
     * @param PHPUnit_Framework_TestCase $test
     */
    public function startTransaction(PHPUnit_Framework_TestCase $test)
    {
        $this->_applyFixtures($this->_getFixtures('method', $test) ?: $this->_getFixtures('class', $test));
    }

    /**
     * Handler for 'rollbackTransaction' event
     */
    public function rollbackTransaction()
    {
        $this->_revertFixtures();
    }

    /**
     * Retrieve fixtures from annotation
     *
     * @param string $scope 'class' or 'method'
     * @param PHPUnit_Framework_TestCase $test
     * @return array
     * @throws Magento_Exception
     */
    protected function _getFixtures($scope, PHPUnit_Framework_TestCase $test)
    {
        $annotations = $test->getAnnotations();
        $result = array();
        if (!empty($annotations[$scope]['magentoDataFixture'])) {
            foreach ($annotations[$scope]['magentoDataFixture'] as $fixture) {
                if (strpos($fixture, '\\') !== false) {
                    // usage of a single directory separator symbol streamlines search across the source code
                    throw new Magento_Exception('Directory separator "\\" is prohibited in fixture declaration.');
                }
                $fixtureMethod = array(get_class($test), $fixture);
                if (is_callable($fixtureMethod)) {
                    $result[] = $fixtureMethod;
                } else {
                    $result[] = $this->_fixtureBaseDir . DIRECTORY_SEPARATOR . $fixture;
                }
            }
        }
        return $result;
    }

    /**
     * Execute single fixture script
     *
     * @param string|array $fixture
     */
    protected function _applyOneFixture($fixture)
    {
        if (is_callable($fixture)) {
            call_user_func($fixture);
        } else {
            require($fixture);
        }
    }

    /**
     * Execute fixture scripts if any
     *
     * @param array $fixtures
     * @throws Magento_Exception
     */
    protected function _applyFixtures(array $fixtures)
    {
        /* Execute fixture scripts */
        foreach ($fixtures as $oneFixture) {
            /* Skip already applied fixtures */
            if (in_array($oneFixture, $this->_appliedFixtures, true)) {
                continue;
            }
            $this->_applyOneFixture($oneFixture);
            $this->_appliedFixtures[] = $oneFixture;
        }
    }

    /**
     * Revert changes done by fixtures
     */
    protected function _revertFixtures()
    {
        foreach ($this->_appliedFixtures as $fixture) {
            if (is_callable($fixture)) {
                $fixture[1] .= 'Rollback';
                if (is_callable($fixture)) {
                    $this->_applyOneFixture($fixture);
                }
            } else {
                $fileInfo = pathinfo($fixture);
                $extension = '';
                if (isset($fileInfo['extension'])) {
                    $extension = '.' . $fileInfo['extension'];
                }
                $rollbackScript = $fileInfo['dirname'] . '/' . $fileInfo['filename'] . '_rollback' . $extension;
                if (file_exists($rollbackScript)) {
                    $this->_applyOneFixture($rollbackScript);
                }
            }
        }
        $this->_appliedFixtures = array();
    }
}
