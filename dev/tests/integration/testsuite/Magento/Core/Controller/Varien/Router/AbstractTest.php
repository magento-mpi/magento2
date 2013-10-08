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

namespace Magento\Core\Controller\Varien\Router;

class AbstractTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var \Magento\App\Router\AbstractRouter
     */
    protected $_model;

    protected function setUp()
    {
        $this->_model = $this->getMockForAbstractClass('Magento\Core\Controller\Varien\Router\AbstractRouter',
            array(), '', false
        );
    }

    public function testGetSetFront()
    {
        $expected = \Magento\TestFramework\Helper\Bootstrap::getObjectManager()
            ->create('Magento\App\FrontController');
        $this->assertNull($this->_model->getFront());
        $this->_model->setFront($expected);
        $this->assertSame($expected, $this->_model->getFront());
    }
}
