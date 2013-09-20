<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_ImportExport
 * @subpackage  integration_tests
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * @magentoAppArea adminhtml
 */
namespace Magento\ImportExport\Controller\Adminhtml;

class ExportTest extends \Magento\Backend\Utility\Controller
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
            'products'                    => array('$entityType' => 'catalog_product'),
            'customers'                   => array('$entityType' => 'customer'),
            // customer entities
            'customers_customer_entities' => array('$entityType' => 'customer', '$customerEntityType' => 'customer'),
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
     * @param string $customerEntityType
     */
    public function testGetFilterAction($entityType, $customerEntityType = null)
    {
        $this->getRequest()->setParam('isAjax', true);

        // Provide X_REQUESTED_WITH header in response to mark next action as ajax
        $_SERVER['HTTP_X_REQUESTED_WITH'] = 'XMLHttpRequest';

        $url = 'backend/admin/export/getFilter/entity/' . $entityType;
        if ($customerEntityType) {
            $url .= '/customer_entity/' . $customerEntityType;
        }
        $this->dispatch($url);

        $this->assertContains('<div id="export_filter_grid"', $this->getResponse()->getBody());
    }

    /**
     * Test index action
     */
    public function testIndexAction()
    {
        $this->dispatch('backend/admin/export/index');

        $body = $this->getResponse()->getBody();
        $this->assertSelectCount('fieldset#base_fieldset', 1, $body);
        $this->assertSelectCount('fieldset#base_fieldset div.field', 2, $body);
    }
}
