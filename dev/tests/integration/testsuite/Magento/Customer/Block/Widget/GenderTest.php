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

class GenderTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var \Magento\Customer\Block\Widget\Gender
     */
    protected $_block;

    protected function setUp()
    {
        $this->_block = \Mage::app()->getLayout()->createBlock('Magento\Customer\Block\Widget\Gender');
    }

    public function testGetGenderOptions()
    {
        $options = $this->_block->getGenderOptions();
        $this->assertInternalType('array', $options);
        $this->assertNotEmpty($options);
    }
}
