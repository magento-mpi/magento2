<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     unit_tests
 * @copyright   {copyright}
 * @license     {license_link}
 */

class Magento_Authorization_Policy_DefaultTest extends PHPUnit_Framework_TestCase
{
    /**
     * @var Magento_Authorization_Policy_Default
     */
    protected $_model;

    protected function setUp()
    {
        $this->_model = new Magento_Authorization_Policy_Default();
    }

    public function testIsAllowedReturnsTrueForAnyResource()
    {
        $this->assertTrue($this->_model->isAllowed('any_role', 'any_resource'));
    }
}
