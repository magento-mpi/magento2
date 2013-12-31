<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Catalog\Model;

class UrlTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var \Magento\Catalog\Model\Url
     */
    protected $_model;

    /**
     * @var \Magento\TestFramework\Helper\ObjectManager
     */
    protected $_objectManager;

    protected function setUp()
    {
        $this->_objectManager = new \Magento\TestFramework\Helper\ObjectManager($this);
        $this->_model = $this->_objectManager->getObject('Magento\Catalog\Model\Url');
    }

    public function testGenerateUniqueIdPath()
    {
        $path = $this->_model->generateUniqueIdPath();
        $this->assertNotContains('.', $path);
        $this->assertContains('_', $path);
        $this->assertNotEquals($path, $this->_model->generateUniqueIdPath());
    }
}
