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
     * @var \Magento\Authorization\Policy\DefaultPolicy
     */
    protected $_model;

    protected function setUp()
    {
        $this->_model = new \Magento\Authorization\Policy\DefaultPolicy();
    }

    public function testIsAllowedReturnsTrueForAnyResource()
    {
        $this->assertTrue($this->_model->isAllowed('any_role', 'any_resource'));
    }
}
