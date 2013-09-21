<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_ScheduledImportExport
 * @subpackage  integration_tests
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * @magentoAppArea adminhtml
 */
class Magento_ScheduledImportExport_Controller_Adminhtml_Scheduled_OperationTest
    extends Magento_Backend_Utility_Controller
{
    /**
     * Set value of $_SERVER['HTTP_X_REQUESTED_WITH'] parameter here
     *
     * @var string
     */
    protected $_httpXRequestedWith;

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

    /**
     * Get some required fields list to check whether they are present on edit form
     *
     * @return array
     */
    public function getEditActionDataProvider()
    {
        return array(
            'export' => array(
                '$expectedContains' => array(
                    'name',
                    'entity_type',
                    'file_format',
                    'server_type',
                    'file_path',
                    'freq',
                    'status',
                    'email_receiver',
                    'email_sender',
                    'email_template',
                    'email_copy_method'
                )
            )
        );
    }

    /**
     * Test edit action
     *
     * @magentoDataFixture Magento/ScheduledImportExport/_files/operation.php
     * @dataProvider getEditActionDataProvider
     *
     * @param array $expectedContains expected filed names list
     */
    public function testEditAction($expectedContains)
    {
        /** @var $collection Magento_ScheduledImportExport_Model_Resource_Scheduled_Operation_Collection */
        $collection = Mage::getModel('Magento_ScheduledImportExport_Model_Resource_Scheduled_Operation_Collection');
        $this->assertCount(1, $collection->getItems());
        $operation = $collection->getFirstItem();

        $this->dispatch('backend/admin/scheduled_operation/edit/id/' . $operation->getId());

        foreach ($expectedContains as $expectedFieldName) {
            $this->assertContains($expectedFieldName, $this->getResponse()->getBody());
        }
    }

    /**
     * Test cron action
     *
     * @magentoDataFixture Magento/ScheduledImportExport/_files/operation.php
     * @magentoDataFixture Magento/Catalog/_files/products_new.php
     */
    public function testCronAction()
    {
        Magento_TestFramework_Helper_Bootstrap::getObjectManager()->get('Magento_Core_Model_Session')
            ->getMesseges(true);
        /** @var $collection Magento_ScheduledImportExport_Model_Resource_Scheduled_Operation_Collection */
        $collection = Mage::getModel('Magento_ScheduledImportExport_Model_Resource_Scheduled_Operation_Collection');
        $this->assertCount(1, $collection->getItems());
        $operation = $collection->getFirstItem();

        // Create export directory if not exist
        $varDir = Mage::getBaseDir('var');
        $exportDir = $varDir . DS . 'export';
        if (!is_dir($exportDir)) {
            mkdir($exportDir, 0777);
        }

        // Change current working directory to allow save export results
        $cwd = getcwd();
        chdir($varDir);

        $this->dispatch('backend/admin/scheduled_operation/cron/operation/' . $operation->getId());

        // Restore current working directory
        chdir($cwd);

        $this->assertSessionMessages($this->isEmpty(), Magento_Core_Model_Message::ERROR);
        $this->assertSessionMessages($this->logicalNot($this->isEmpty()), Magento_Core_Model_Message::SUCCESS);
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
        // Provide X_REQUESTED_WITH header in response to mark next action as ajax
        $_SERVER['HTTP_X_REQUESTED_WITH'] = 'XMLHttpRequest';

        $this->dispatch('backend/admin/scheduled_operation/getFilter/entity/' . $entityType);

        $this->assertContains('<div id="export_filter_grid"', $this->getResponse()->getBody());
    }
}
