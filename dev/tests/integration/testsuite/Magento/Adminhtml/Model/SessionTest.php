<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Adminhtml
 * @subpackage  integration_tests
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * @magentoAppArea adminhtml
 */
class Magento_Adminhtml_Model_SessionTest extends PHPUnit_Framework_TestCase
{
    public function testConstructor()
    {
        $this->assertInstanceOf(
            'Magento_Backend_Model_Session',
            Magento_TestFramework_Helper_Bootstrap::getObjectManager()->create('Magento_Adminhtml_Model_Session')
        );
    }
}
