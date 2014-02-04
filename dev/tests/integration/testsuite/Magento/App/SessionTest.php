<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @subpackage  integration_tests
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\App;

/**
 * @SuppressWarnings(PHPMD.UnusedFormalParameter)
 * @magentoAppArea frontend
 */
class SessionTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var \Magento\ObjectManager
     */
    protected $_objectManager;

    protected function setUp()
    {
        $this->_objectManager = \Magento\TestFramework\Helper\Bootstrap::getObjectManager();
    }

    /**
     * @dataProvider classesProvider
     * @param $className
     */
    public function testDispatch($className)
    {
        $this->_objectManager->create($className);
    }

    /**
     * @return array
     */
    public function classesProvider()
    {
        return array(
            array('Magento\PageCache\Model\App\FrontController\HeaderPlugin'),
            array('Magento\Captcha\Model\Observer')
        );
    }
}
