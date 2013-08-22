<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Core
 * @subpackage  integration_tests
 * @copyright   {copyright}
 * @license     {license_link}
 */

class Magento_Core_Controller_Varien_Router_AbstractTest extends PHPUnit_Framework_TestCase
{
    /**
     * @var Magento_Core_Controller_Varien_Router_Abstract
     */
    protected $_model;

    protected function setUp()
    {
        $this->_model = $this->getMockForAbstractClass('Magento_Core_Controller_Varien_Router_Abstract', array(), '',
            false
        );
    }

    public function testGetSetFront()
    {
        $expected = Mage::getModel('Magento_Core_Controller_Varien_Front');
        $this->assertNull($this->_model->getFront());
        $this->_model->setFront($expected);
        $this->assertSame($expected, $this->_model->getFront());
    }
}
