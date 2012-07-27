<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Mage_Adminhtml
 * @subpackage  integration_tests
 * @copyright   {copyright}
 * @license     {license_link}
 */

class Mage_Adminhtml_Block_Urlrewrite_Cms_Page_Edit_FormTest extends PHPUnit_Framework_TestCase
{
    /**
     * @var Varien_Data_Form
     */
    protected $_form = null;

    /**
     * Initialize form
     */
    protected function _initForm()
    {
        $layout = new Mage_Core_Model_Layout();
        /** @var $block Mage_Adminhtml_Block_Urlrewrite_Cms_Page_Edit_Form */
        $block = $layout->createBlock('Mage_Adminhtml_Block_Urlrewrite_Cms_Page_Edit_Form', 'block');
        $block->toHtml();
        $this->_form = $block->getForm();
    }

    /**
     * Unset block
     */
    protected function tearDown()
    {
        Mage::unregister('current_cms_page');
        unset($this->_form);
        parent::tearDown();
    }

    /**
     * Check _formPostInit set expected fields values
     *
     * @covers Mage_Adminhtml_Block_Urlrewrite_Cms_Page_Edit_Form::_formPostInit
     *
     * @dataProvider formPostInitDataProvider
     *
     * @param array $cmsPageData
     * @param string $action
     * @param string $idPath
     * @param string $requestPath
     * @param string $targetPath
     */
    public function testFormPostInit($cmsPageData, $action, $idPath, $requestPath, $targetPath)
    {
        if ($cmsPageData) {
            Mage::register('current_cms_page', new Varien_Object($cmsPageData));
        }
        $this->_initForm();
        $this->assertContains($action, $this->_form->getAction());

        $this->assertEquals($idPath, $this->_form->getElement('id_path')->getValue());
        $this->assertEquals($requestPath, $this->_form->getElement('request_path')->getValue());
        $this->assertEquals($targetPath, $this->_form->getElement('target_path')->getValue());

        $this->assertTrue($this->_form->getElement('id_path')->getData('disabled'));
        $this->assertTrue($this->_form->getElement('target_path')->getData('disabled'));
    }

    /**
     * @magentoAppIsolation enabled
     * @magentoDataFixture Mage/Core/_files/store.php
     */
    public function testGetEntityStores()
    {
        Mage::register('current_cms_page', $this->_getCmsPageWithStoresMock(array(1)));
        $this->_initForm();

        $expectedStores = array(
            array(
                'label' => 'Main Website',
                'value' => array()
            ),
            array(
                'label' => '    Main Website Store',
                'value' => array(
                    array(
                        'label' => '    Default Store View',
                        'value' => 1
                    )
                )
            )
        );
        $this->assertEquals($expectedStores, $this->_form->getElement('store_id')->getValues());
    }

    /**
     * Check exception is thrown when product does not associated with stores
     *
     * @magentoAppIsolation enabled
     * @magentoDataFixture Mage/Core/_files/store.php
     *
     * @expectedException Mage_Core_Model_Store_Exception
     * @expectedExceptionMessage Chosen cms page does not associated with any website.
     */
    public function testGetEntityStoresProductStoresException()
    {
        Mage::register('current_cms_page', $this->_getCmsPageWithStoresMock(array()));
        $this->_initForm();
    }

    /**
     * Data provider for testing formPostInit
     * 1) Cms page is selected
     *
     * @static
     * @return array
     */
    public static function formPostInitDataProvider()
    {
        return array(
            array(
                array('id' => 3, 'identifier' => 'cms-page'),
                'cms_page/3', 'cms_page/3', 'cms-page', 'cms/page/view/page_id/3'
            )
        );
    }

    protected function _getCmsPageWithStoresMock($stores)
    {
        $resourceMock = $this->getMockBuilder('Mage_Cms_Model_Resource_Page')
            ->setMethods(array('lookupStoreIds'))
            ->getMock();
        $resourceMock->expects($this->any())
            ->method('lookupStoreIds')
            ->will($this->returnValue($stores));

        $cmsPageMock = $this->getMockBuilder('Mage_Cms_Model_Page')
            ->setMethods(array('getResource', 'getId'))
            ->getMock();
        $cmsPageMock->expects($this->any())
            ->method('getId')
            ->will($this->returnValue(1));
        $cmsPageMock->expects($this->any())
            ->method('getResource')
            ->will($this->returnValue($resourceMock));

        return $cmsPageMock;
    }
}
