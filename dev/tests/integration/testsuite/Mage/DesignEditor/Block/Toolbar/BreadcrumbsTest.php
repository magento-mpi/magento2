<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Mage_DesignEditor
 * @subpackage  integration_tests
 * @copyright   {copyright}
 * @license     {license_link}
 */

class Mage_DesignEditor_Block_Toolbar_BreadcrumbsTest extends PHPUnit_Framework_TestCase
{
    /**
     * @var Mage_DesignEditor_Block_Toolbar_Breadcrumbs
     */
    protected $_block;

    protected function setUp()
    {
        $layoutUtility = new Mage_Core_Utility_Layout($this);
        $pageTypesFixture = __DIR__ . '/../../../Core/Model/Layout/_files/_handles.xml';
        $this->_block = new Mage_DesignEditor_Block_Toolbar_Breadcrumbs(
            array('template' => 'toolbar/breadcrumbs.phtml')
        );
        $this->_block->setLayout($layoutUtility->getLayoutFromFixture($pageTypesFixture));
    }

    /**
     * Set the current route/controller/action
     *
     * @param string $routName
     * @param string $controllerName
     * @param string $actionName
     */
    protected function _setControllerAction($routName, $controllerName, $actionName)
    {
        /** @var $controllerAction Mage_Core_Controller_Varien_Action */
        $controllerAction = $this->getMockForAbstractClass(
            'Mage_Core_Controller_Varien_Action',
            array(new Magento_Test_Request(), new Magento_Test_Response())
        );
        /* Note: controller action instance registers itself within the front controller immediately after creation */
        $controllerAction->getRequest()
            ->setRouteName($routName)
            ->setControllerName($controllerName)
            ->setActionName($actionName);
    }

    public function testGetBreadcrumbsFromPageHandles()
    {
        $this->_block->getLayout()->getUpdate()->addPageHandles(array('catalog_product_view_type_simple'));
        $this->assertEquals(
            require(__DIR__ . '/_files/_breadcrumbs_simple_product.php'),
            $this->_block->getBreadcrumbs()
        );
    }

    /**
     * @magentoAppIsolation enabled
     */
    public function testGetBreadcrumbsFromDefaultLayoutHandle()
    {
        $this->_setControllerAction('catalog', 'product_view', 'type_simple');
        $this->assertEquals(
            require(__DIR__ . '/_files/_breadcrumbs_simple_product.php'),
            $this->_block->getBreadcrumbs()
        );
    }

    public function testToHtmlFromPageHandles()
    {
        $this->_block->getLayout()->getUpdate()->addPageHandles(array('catalog_product_view_type_simple'));
        $this->assertXmlStringEqualsXmlFile(
            __DIR__ . '/_files/_breadcrumbs_simple_product.html',
            $this->_block->toHtml()
        );
    }

    /**
     * @magentoAppIsolation enabled
     */
    public function testToHtmlFromDefaultLayoutHandle()
    {
        $this->_setControllerAction('catalog', 'product_view', 'type_simple');
        $this->assertXmlStringEqualsXmlFile(
            __DIR__ . '/_files/_breadcrumbs_simple_product.html',
            $this->_block->toHtml()
        );
    }
}
