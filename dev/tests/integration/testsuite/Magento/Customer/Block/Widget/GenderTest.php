<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Customer
 * @subpackage  integration_tests
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Customer\Block\Widget;
use Magento\TestFramework\Helper\Bootstrap;

class GenderTest extends \PHPUnit_Framework_TestCase
{
    /** @var Gender */
    protected $_block;

    /**
     * Test initialization and set up. Create the Gender block.
     * @return void
     */
    protected function setUp()
    {
        $this->_block = Bootstrap::getObjectManager()->get('Magento\View\LayoutInterface')
            ->createBlock('Magento\Customer\Block\Widget\Gender');
    }

    /**
     * Test the Gender::getGenderOptions() method.
     * @return void
     */
    public function testGetGenderOptions()
    {
        $options = $this->_block->getGenderOptions();
        $this->assertInternalType('array', $options);
        $this->assertNotEmpty($options);
    }
}
