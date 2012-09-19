<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Mage_Page
 * @subpackage  integration_tests
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Test class for Mage_Page_Helper_Robots
 */
class Mage_Page_Helper_RobotsTest extends PHPUnit_Framework_TestCase
{
    /**
     * @var Mage_Page_Helper_Robots
     */
    protected $_helper;

    protected function setUp()
    {
        $this->_helper = new Mage_Page_Helper_Robots();
    }

    /**
     * @covers Mage_Page_Helper_RobotsTest::getRobotsDefaultCustomInstructions
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
