<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Core
 * @subpackage  unit_tests
 * @copyright   {copyright}
 * @license     {license_link}
 */

class Magento_Core_Model_PageTest extends PHPUnit_Framework_TestCase
{
    /**
     * @var \Magento\Core\Model\Page
     */
    protected $_object;

    /**
     * @var
     */
    protected $_pageAssets;

    protected function setUp()
    {
        $this->_pageAssets = new \Magento\Core\Model\Page\Asset\Collection;
        $this->_object = new \Magento\Core\Model\Page($this->_pageAssets);
    }

    protected function tearDown()
    {
        $this->_pageAssets = null;
        $this->_object = null;
    }

    public function testGetAssets()
    {
        $this->assertSame($this->_pageAssets, $this->_object->getAssets());
    }
}
