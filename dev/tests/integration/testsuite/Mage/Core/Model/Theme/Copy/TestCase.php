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

/**
 * Test theme copy functionality
 */
class Mage_Core_Model_Theme_Copy_TestCase extends PHPUnit_Framework_TestCase
{
    /**
     * @var Mage_Core_Model_Theme_Copy_VirtualToStaging
     */
    protected $_model;

    /**
     * @var Mage_Core_Model_Resource_Theme_Collection
     */
    protected $_collection;

    /**
     * Initialize Mage_Core_Model_Theme_Copy_VirtualToStaging model
     */
    protected function setUp()
    {
        $this->_model = $this->_getCopyModel();
        $this->_collection = Mage::getObjectManager()->create('Mage_Core_Model_Resource_Theme_Collection');
    }

    /**
     * @return PHPUnit_Framework_MockObject_MockObject|Mage_Core_Model_Theme_Copy_Abstract
     */
    protected function _getCopyModel()
    {
        $constructorArgs = array(
            $this->_getThemeFactory(),
            $this->_getLayoutLink(),
            $this->_getLayoutUpdate(),
            array()
        );
        return $this->getMockForAbstractClass('Mage_Core_Model_Theme_Copy_Abstract', $constructorArgs);
    }

    /**
     * @return Mage_Core_Model_Theme_Factory
     */
    protected function _getThemeFactory()
    {
        return Mage::getObjectManager()->get('Mage_Core_Model_Theme_Factory');
    }

    /**
     * @return Mage_Core_Model_Layout_Link
     */
    protected function _getLayoutLink()
    {
        return Mage::getObjectManager()->create('Mage_Core_Model_Layout_Link');
    }

    /**
     * @return Mage_Core_Model_Layout_Update
     */
    protected function _getLayoutUpdate()
    {
        return Mage::getObjectManager()->create('Mage_Core_Model_Layout_Update');
    }
}
