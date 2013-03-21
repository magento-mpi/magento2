<?php
/**
 * {license_notice}
 *
 * @category    Tools
 * @package     unit_tests
 * @copyright   {copyright}
 * @license     {license_link}
 */

require_once realpath(dirname(__FILE__) . '/../../../../../../') . '/tools/view/Generator/ThemeDeployment.php';

class Tools_View_Generator_ThemeDeploymentTest extends PHPUnit_Framework_TestCase
{
    /**
     * @var Zend_Log
     */
    protected $_logger;

    public function setUp()
    {
        $this->_logger = new Zend_log(new Zend_Log_Writer_Null());
    }

    /**
     * @param string $permitted
     * @param string $forbidden
     * @param string $exceptionMessage
     * @dataProvider constructorExceptionDataProvider
     */
    public function testConstructorException($permitted, $forbidden, $exceptionMessage)
    {
        $this->setExpectedException('Magento_Exception', $exceptionMessage);
        new Generator_ThemeDeployment($this->_logger, $permitted, $forbidden);
    }

    public static function constructorExceptionDataProvider()
    {
        $conflictPermitted = __DIR__ . '/_files/ThemeDeployment/constructor_exception/permitted.txt';
        $conflictForbidden = __DIR__ . '/_files/ThemeDeployment/constructor_exception/forbidden.txt';
        return array(
            'no permitted config' => array(
                'non_existing_config.txt',
                $conflictForbidden,
                'Config file does not exist: non_existing_config.txt',
            ),
            'no forbidden config' => array(
                $conflictPermitted,
                'non_existing_config.txt',
                'Config file does not exist: non_existing_config.txt',
            ),
            'config conflicts' => array(
                $conflictPermitted,
                $conflictForbidden,
                'Conflicts: the following extensions are added both to permitted and forbidden lists: ' .
                    'conflict1, conflict2',
            ),
        );
    }
}
