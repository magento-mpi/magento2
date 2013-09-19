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
 * Test class for \Magento\Page\Helper\Robots
 */
class Magento_Page_Helper_RobotsTest extends PHPUnit_Framework_TestCase
{
    /**
     * @var \Magento\Page\Helper\Robots
     */
    protected $_helper;

    protected function setUp()
    {
        $this->_helper = Magento_TestFramework_Helper_Bootstrap::getObjectManager()->get('Magento\Page\Helper\Robots');
    }

    /**
     * @covers \Magento\Page\Helper\RobotsTest::getRobotsDefaultCustomInstructions
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
