<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Mage_Launcher
 * @subpackage  unit_tests
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Abstract test case for payment save handlers tests 
 */
abstract class Mage_Launcher_Model_Storelauncher_Payments_Savehandlers_TestCaseAbstract
    extends PHPUnit_Framework_TestCase
{
    /**
     * Retrieve mocked backend configuration model
     *
     * @return Mage_Backend_Model_Config|PHPUnit_Framework_MockObject_MockObject
     */
    protected function _getBackendConfigModelMock()
    {
        return $this->getMock(
            'Mage_Backend_Model_Config',
            array('setSection', 'setGroups', 'save'),
            array(),
            '',
            false
        );
    }
}
