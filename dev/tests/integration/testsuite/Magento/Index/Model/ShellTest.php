<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Index
 * @subpackage  integration_tests
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Index\Model;

class ShellTest extends \PHPUnit_Framework_TestCase
{
    /**
     * Returns prepared model
     *
     * @param string $entryPoint
     * @return \Magento\Index\Model\Shell
     */
    protected function _getModel($entryPoint = 'fake.php')
    {
        return \Mage::getModel('Magento\Index\Model\Shell', array('entryPoint' => $entryPoint));
    }

    /**
     * Returns result of running model - can be real model or mocked one
     *
     * @param \Magento\Index\Model\Shell $model Can be mock
     * @return string
     */
    protected function _run($model)
    {
        ob_start();
        $model->run();
        $result = ob_get_contents();
        ob_end_clean();
        return $result;
    }

    public function testGetUsageHelp()
    {
        $model = $this->_getModel('testme.php');
        $this->assertContains('testme.php', $model->getUsageHelp());
    }

    public function testRunWithoutParams()
    {
        $model = $this->_getModel('testme.php');
        $result = $this->_run($model);
        $this->assertContains('testme.php', $result);
        $this->assertContains('index', $result); // Something about indexes
    }

    public function testRunIndexList()
    {
        $model = $this->_getModel('testme.php');
        $model->setRawArgs(array('testme.php', '--', 'status'));
        $result = $this->_run($model);

        $this->assertNotContains('testme.php', $result);
        $this->assertNotContains('Usage:', $result);
        $this->assertNotEmpty($result);
    }

    /**
     * @param string $indexCode
     * @param bool $expectedHasErrors
     *
     * @dataProvider hasErrorsDataProvider
     */
    public function testHasErrors($param, $expectedHasErrors)
    {
        $model = $this->_getModel('testme.php');
        $model->setRawArgs(array('testme.php', '--', $param));
        $this->_run($model);

        $this->assertEquals($expectedHasErrors, $model->hasErrors());
    }

    /**
     * @return array
     */
    public function hasErrorsDataProvider()
    {
        return array(
            'execution without issues' => array('info', false),
            'issue with wrong index' => array('--reindex=wrong_index_code', true),
        );
    }
}
