<?php
/**
 * {license_notice}
 *
 * @category    tests
 * @package     static
 * @subpackage  Legacy
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Coverage of obsolete nodes in layout
 */
class Legacy_LayoutTest extends PHPUnit_Framework_TestCase
{
    /**
     * List of obsolete nodes
     *
     * @var array
     */
    protected $_obsoleteNodes = array(
        'PRODUCT_TYPE_simple', 'PRODUCT_TYPE_configurable', 'PRODUCT_TYPE_grouped', 'PRODUCT_TYPE_bundle',
        'PRODUCT_TYPE_virtual', 'PRODUCT_TYPE_downloadable', 'PRODUCT_TYPE_giftcard',
        'catalog_category_default', 'catalog_category_layered', 'catalog_category_layered_nochildren',
        'customer_logged_in', 'customer_logged_out', 'customer_logged_in_psc_handle', 'customer_logged_out_psc_handle',
        'cms_page', 'sku_failed_products_handle', 'catalog_product_send'
    );

    /**
     * List of obsolete references per handle
     *
     * @var array
     */
    protected $_obsoleteReferences = array(
        'adminhtml_user_edit' => array(
            'adminhtml.permissions.user.edit.tabs',
            'adminhtml.permission.user.edit.tabs',
            'adminhtml.permissions.user.edit',
            'adminhtml.permission.user.edit',
            'adminhtml.permissions.user.roles.grid.js',
            'adminhtml.permission.user.roles.grid.js',
            'adminhtml.permissions.user.edit.tab.roles',
            'adminhtml.permissions.user.edit.tab.roles.js'
        ),
        'adminhtml_user_role_index' => array(
            'adminhtml.permission.role.index',
            'adminhtml.permissions.role.index',
            'adminhtml.permissions.role.grid'
        ),
        'adminhtml_user_role_rolegrid' => array(
            'adminhtml.permission.role.grid',
            'adminhtml.permissions.role.grid'
        ),
        'adminhtml_user_role_editrole' => array(
            'adminhtml.permissions.editroles',
            'adminhtml.permissions.tab.rolesedit',
            'adminhtml.permission.roles.users.grid.js',
            'adminhtml.permissions.roles.users.grid.js',
            'adminhtml.permission.role.buttons',
            'adminhtml.permissions.role.buttons',
            'adminhtml.permission.role.edit.gws'
        ),
        'adminhtml_user_role_editrolegrid' => array(
            'adminhtml.permission.role.grid.user',
            'adminhtml.permissions.role.grid.user'
        ),
        'adminhtml_user_index' => array(
            'adminhtml.permission.user.index',
            'adminhtml.permissions.user.index'
        ),
        'adminhtml_user_rolegrid' => array(
            'adminhtml.permissions.user.rolegrid',
            'adminhtml.permission.user.rolegrid'
        ),
        'adminhtml_user_rolesgrid' => array(
            'adminhtml.permissions.user.rolesgrid',
            'adminhtml.permission.user.rolesgrid'
        )
    );

    /**
     * @param string $layoutFile
     * @dataProvider layoutFileDataProvider
     */
    public function testLayoutFile($layoutFile)
    {
        $layoutXml = simplexml_load_file($layoutFile);

        $this->_testObsoleteReferences($layoutXml);

        $selectorHeadBlock = '
            (name()="block" or name()="reference") and (@name="head" or @name="convert_root_head" or @name="vde_head")
        ';
        $this->assertSame(array(),
            $layoutXml->xpath(
                '//*[' . $selectorHeadBlock . ']/action[@method="addItem"]'
            ),
            'Mage_Page_Block_Html_Head::addItem is obsolete. Use addCss()/addJs() instead.'
        );
        $this->assertSame(array(),
            $layoutXml->xpath(
                '//action[@method="addJs" or @method="addCss"]/parent::*[not(' . $selectorHeadBlock . ')]'
            ),
            "Calls addCss/addJs are allowed within the 'head' block only. Verify integrity of the nodes nesting."
        );
        $this->assertSame(array(),
            $layoutXml->xpath('/layout//*[@output="toHtml"]'), 'output="toHtml" is obsolete. Use output="1"'
        );
        foreach ($layoutXml as $handle) {
            $this->assertNotContains($handle->getName(), $this->_obsoleteNodes, 'Layout handle was removed.');
        }
        foreach ($layoutXml->xpath('@helper') as $action) {
            $this->assertNotContains('/', $action->getAtrtibute('helper'));
            $this->assertContains('::', $action->getAtrtibute('helper'));
        }

        if (false !== strpos($layoutFile, 'app/code/Magento/Adminhtml/view/adminhtml/layout/adminhtml_sales_order')) {
            $this->markTestIncomplete("The file {$layoutFile} has to use Magento_Core_Block_Text_List, \n"
                . 'there is no solution to get rid of it right now.'
            );
        }
        $this->assertSame(array(),
            $layoutXml->xpath('/layout//block[@type="Magento_Core_Block_Text_List"]'),
            'The class Magento_Core_Block_Text_List is not supposed to be used in layout anymore.'
        );
    }

    /**
     * @param SimpleXMLElement $layoutXml
     */
    protected function _testObsoleteReferences($layoutXml)
    {
        foreach ($layoutXml as $handle) {
            if (isset($this->_obsoleteReferences[$handle->getName()])) {
                foreach ($handle->xpath('reference') as $reference) {
                    $this->assertNotContains(
                        (string)$reference['name'],
                        $this->_obsoleteReferences[$handle->getName()],
                        'The block being referenced is removed.'
                    );
                }
            }
        }
    }

    /**
     * @return array
     */
    public function layoutFileDataProvider()
    {
        return Utility_Files::init()->getLayoutFiles();
    }
}
