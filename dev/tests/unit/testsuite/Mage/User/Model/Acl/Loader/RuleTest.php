<?php
/**
 * {license_notice}
 *
 * @category    Mage
 * @package     Mage_User
 * @subpackage  unit_tests
 * @copyright   {copyright}
 * @license     {license_link}
 */

class Mage_User_Model_Acl_Loader_RuleTest extends PHPUnit_Framework_TestCase
{
    /**
     * @var
     */
    protected $_model;

    public function setUp()
    {
        $this->_model = new Mage_User_Model_Acl_Loader_Rule(array(
            'resource' => $this->getMock('Mage_Core_Model_Resource')
        ));
    }
}
