<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Mage_Core
 * @subpackage  integration_tests
 * @copyright   {copyright}
 * @license     {license_link}
 */

class Mage_Core_Controller_Varien_Router_AbstractTest extends PHPUnit_Framework_TestCase
{
    /**
     * @var Mage_Core_Controller_Varien_Router_Abstract
     */
    protected $_model;

    protected function setUp()
    {
        $this->_model = $this->getMockForAbstractClass('Mage_Core_Controller_Varien_Router_Abstract');
    }

    public function testGetFront()
    {
        $frontController = $this->_model->getFront();
        $this->assertInstanceOf('Mage_Core_Controller_Varien_Front', $frontController);
        $this->assertSame($frontController, $this->_model->getFront());
    }
}
