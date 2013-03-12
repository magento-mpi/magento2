<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Mage_Core
 * @subpackage  unit_tests
 * @copyright   {copyright}
 * @license     {license_link}
 */

class Mage_Core_Model_PageTest extends PHPUnit_Framework_TestCase
{
    /**
     * @var Mage_Core_Model_Page
     */
    protected $_object;

    protected function setUp()
    {
        $this->_object = new Mage_Core_Model_Page();
    }

    public function testGetAssets()
    {
        $actualResult = $this->_object->getAssets();
        $this->assertInstanceOf('Mage_Core_Model_Page_Asset_Collection', $actualResult);
        $this->assertSame($actualResult, $this->_object->getAssets(), 'The same assets are to be returned.');
    }
}
