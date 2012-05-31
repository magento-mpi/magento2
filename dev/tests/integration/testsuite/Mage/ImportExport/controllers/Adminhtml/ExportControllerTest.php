<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Mage_ImportExport
 * @subpackage  integration_tests
 * @copyright   {copyright}
 * @license     {license_link}
 */

class Mage_ImportExport_Adminhtml_ExportControllerTest extends Mage_Adminhtml_Utility_Controller
{
    /**
     * Set value of $_SERVER['HTTP_X_REQUESTED_WITH'] parameter here
     *
     * @var string
     */
    protected $_httpXRequestedWith;

    /**
     * Get possible entity types
     *
     * @return array
     */
    public function getEntityTypesDataProvider()
    {
        return array(
            'products'  => array('$entityType' => 'catalog_product'),
            'customers' => array('$entityType' => 'customer')
        );
    }

    protected function setUp()
    {
        parent::setUp();

        if (isset($_SERVER['HTTP_X_REQUESTED_WITH'])) {
            $this->_httpXRequestedWith = $_SERVER['HTTP_X_REQUESTED_WITH'];
        }
    }

    protected function tearDown()
    {
        if (!is_null($this->_httpXRequestedWith)) {
            $_SERVER['HTTP_X_REQUESTED_WITH'] = $this->_httpXRequestedWith;
        }

        parent::tearDown();
    }

    /**
     * Test getFilter action
     *
     * @dataProvider getEntityTypesDataProvider
     *
     * @param string $entityType
     */
    public function testGetFilterAction($entityType)
    {
        $this->getRequest()->setParam('isAjax', true);

        // Provide X_REQUESTED_WITH header in response to mark next action as ajax
        $_SERVER['HTTP_X_REQUESTED_WITH'] = 'XMLHttpRequest';

        $this->dispatch('admin/export/getFilter/entity/' . $entityType);

        $this->assertContains('<div id="export_filter_grid"', $this->getResponse()->getBody());
    }

    /**
     * Test index action
     */
    public function testIndexAction()
    {
        $this->dispatch('admin/export/index');

        $body = $this->getResponse()->getBody();
        $this->assertSelectCount('div#head-export_format_version', 1, $body);
        $this->assertSelectCount('div#export_format_version', 1, $body);
    }
}
