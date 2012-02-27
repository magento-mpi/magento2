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
 * Test class for Abstract Utility
 */
class Magento_Test_Utility_AbstractTest extends PHPUnit_Framework_TestCase
{
    public function testAbstractUtility()
    {
        $utility = $this->getMock('Magento_Test_Utility_Abstract', null, array($this));

        $this->assertTrue(($utility->getTestCase() instanceof PHPUnit_Framework_TestCase));
    }
}
