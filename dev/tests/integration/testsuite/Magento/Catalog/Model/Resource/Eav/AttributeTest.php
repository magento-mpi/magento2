<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Catalog
 * @subpackage  integration_tests
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Catalog\Model\Resource\Eav;

class AttributeTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var \Magento\Catalog\Model\Resource\Eav\Attribute
     */
    protected $_model;

    protected function setUp()
    {
        $this->_model= \Mage::getResourceModel('Magento\Catalog\Model\Resource\Eav\Attribute');
    }

    public function testCRUD()
    {
        $this->_model->setAttributeCode('test')
            ->setEntityTypeId(\Mage::getSingleton('Magento\Eav\Model\Config')
            ->getEntityType('catalog_product')->getId())->setFrontendLabel('test');
        $crud = new \Magento\TestFramework\Entity($this->_model, array('frontend_label' => uniqid()));
        $crud->testCrud();
    }
}
