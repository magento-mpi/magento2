<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Page
 * @subpackage  integration_tests
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Test class for Magento_Page_Helper_Robots
 */
class Magento_Page_Helper_RobotsTest extends PHPUnit_Framework_TestCase
{
    /**
     * @var Magento_Page_Helper_Robots
     */
    protected $_helper;

    protected function setUp()
    {
        $this->_helper = Mage::helper('Magento_Page_Helper_Robots');
    }

    /**
     * @covers Magento_Page_Helper_RobotsTest::getRobotsDefaultCustomInstructions
     */
    public function testGetRobotsDefaultCustomInstructions()
    {
        $this->assertStringEqualsFile(
            __DIR__ . '/../_files/robots.txt',
            $this->_helper->getRobotsDefaultCustomInstructions(),
            'robots.txt default custom instructions are invalid'
        );
    }
}
