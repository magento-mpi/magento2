<?php
/**
 * Same as obsolete_classes.php, but specific to Magento EE
 *
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
return array(
    array('Magento\CatalogEvent\Block\Adminhtml\Event\Grid'),
    array('Magento\AdvancedCheckout\Block\Adminhtml\Sku\Errors\Grid'),
    array(
        'Magento\CustomAttributeManagement\Block\Form\Template',
        'Magento\View\Element\Template with renderers as child blocks'
    ),
    array('Magento\CustomerCustomAttributes\Block\Form\Renderer\AbstractRenderer'),
    array('Magento\CustomerCustomAttributes\Block\Form\Renderer\Boolean'),
    array('Magento\CustomerCustomAttributes\Block\Form\Renderer\Date'),
    array('Magento\CustomerCustomAttributes\Block\Form\Renderer\File'),
    array('Magento\CustomerCustomAttributes\Block\Form\Renderer\Image'),
    array('Magento\CustomerCustomAttributes\Block\Form\Renderer\Multiline'),
    array('Magento\CustomerCustomAttributes\Block\Form\Renderer\Multiselect'),
    array('Magento\CustomerCustomAttributes\Block\Form\Renderer\Select'),
    array('Magento\CustomerCustomAttributes\Block\Form\Renderer\Text'),
    array('Magento\CustomerCustomAttributes\Block\Form\Renderer\Textarea'),
    array('Magento\CustomerCustomAttributes\Block\Form\Template'),
    array('Magento\GiftCardAccount\Block\Adminhtml\Giftcardaccount\Grid'),
    array('Magento\GiftCard\Block\Email\Generated'),
    array('Magento\GiftRegistry\Block\Adminhtml\Customer\View'),
    array('Magento\GiftRegistry\Block\Adminhtml\Giftregistry\Grid'),
    array('Magento\ScheduledImportExport\Block\Adminhtml\Scheduled\Operation\Grid'),
    array('Magento\Invitation\Block\Adminhtml\Invitation\Grid'),
    array('Magento\Invitation\Block\Adminhtml\Report\Invitation\Customer\Grid'),
    array('Magento\Invitation\Block\Adminhtml\Report\Invitation\Order\Grid'),
    array('Magento\Invitation\Block\Adminhtml\Report\Invitation\General\Grid'),
    array('Magento\Logging\Block\Adminhtml\Archive\Grid'),
    array('Magento\Logging\Block\Adminhtml\Index\Grid'),
    array('Magento\Pci\Block\Adminhtml\Locks\Grid'),
    array('Magento\Reminder\Block\Adminhtml\Reminder\Grid'),
    array('Magento\Reward\Block\Adminhtml\Reward\Rate\Grid'),
    array('Magento\Reward\Block\Customer\Account', 'Magento\Reward\Block\Customer\AccountLink'),
    array('Magento\Search\Model\Indexer\Price'),
    array('Magento\Search\Model\Resource\Suggestions'),
    array('Magento\TargetRule\Block\Adminhtml\Targetrule\Grid'),
    array('Magento\GiftWrapping\Block\Adminhtml\Giftwrapping\Grid'),
    array('Magento\CustomerSegment\Model\Resource\Helper\Mssql'),
    array('Magento\Logging\Model\Resource\Helper\Mssql'),
    array('Magento\Reminder\Model\Resource\Helper\Mssql'),
    array('Magento\SalesArchive\Model\Resource\Helper\Mssql'),
    array('Magento\CustomerSegment\Model\Resource\Helper\Oracle'),
    array('Magento\Logging\Model\Resource\Helper\Oracle'),
    array('Magento\Reminder\Model\Resource\Helper\Oracle'),
    array('Magento\SalesArchive\Model\Resource\Helper\Oracle'),
    array('Magento\Search\Model\ObjectManager\Configurator'),
    array('Varien_Db_Statement_Pdo_Mssql'),
    array('Varien_Db_Adapter_Pdo_Mssql'),
    array('Mage_Backup_Model_Resource_Helper_Mssql'),
    array('Mage_Catalog_Model_Resource_Helper_Mssql'),
    array('Mage_CatalogSearch_Model_Resource_Helper_Mssql'),
    array('Mage_Core_Model_Resource_Helper_Mssql'),
    array('Mage_Eav_Model_Resource_Helper_Mssql'),
    array('Mage_ImportExport_Model_Resource_Helper_Mssql'),
    array('Mage_Log_Model_Resource_Helper_Mssql'),
    array('Mage_Reports_Model_Resource_Helper_Mssql'),
    array('Mage_Sales_Model_Resource_Helper_Mssql'),
    array('Mage_Install_Block_Db_Type_Mssql'),
    array('Mage_Install_Model_Installer_Db_Mssql'),
    array('Varien_Db_Statement_Sqlsrv'),
    array('Mage_Backup_Model_Resource_Helper_Oracle'),
    array('Mage_Catalog_Model_Resource_Helper_Oracle'),
    array('Mage_CatalogSearch_Model_Resource_Helper_Oracle'),
    array('Mage_Eav_Model_Resource_Helper_Oracle'),
    array('Mage_ImportExport_Model_Resource_Helper_Oracle'),
    array('Mage_Log_Model_Resource_Helper_Oracle'),
    array('Mage_Reports_Model_Resource_Helper_Oracle'),
    array('Mage_Sales_Model_Resource_Helper_Oracle'),
    array('Mage_Core_Model_Resource_Helper_Oracle'),
    array('Varien_Db_Adapter_Oracle'),
    array('Mage_Install_Block_Db_Type_Oracle'),
    array('Mage_Install_Model_Installer_Db_Oracle'),
    array('Magento\Queue\Model\Queue'),
    array('Magento\Queue\Model\AddException'),
    array('Magento\Queue\Model\Config'),
    array('Magento\Queue\Model\Config\Gearman'),
    array('Magento\Queue\Model\Event\Handler'),
    array('Magento\Queue\Model\Event\Invoker\Asynchronous'),
    array('Magento\Queue\Model\Resource\Task'),
    array('Magento\Queue\Model\Task'),
    array('Magento\Queue\Model\TaskRepository'),
    array('Magento\Rma\Block\Order\Guest'),
    array('Magento\Rma\Block\Order\Info'),
    array('Magento\Rma\Block\Returns\Info'),
    array('Magento\MultipleWishlist\Block\Links', 'Magento\MultipleWishlist\Block\Link'),
    array('Magento\Reminder\Model\Resource\Setup', '\Magento\Core\Model\Resource\Setup'),
    array('Magento\FullPageCache\Model\Http\Handler'),
    array('Magento\CustomerSegment\Model\Resource\Helper\Mysql4', 'Magento\CustomerSegment\Model\Resource\Helper'),
    array('Magento\SalesArchive\Model\Resource\Helper\Mysql4', 'Magento\SalesArchive\Model\Resource\Helper'),
    array('Magento\Search\Model\Client\Solr\Factory'),
    array('Magento\Search\Model\Client\SolrClient\Factory'),
    array('Magento\License', 'Magento_License'),
    array(
        'Magento\PricePermissions\Controller\Adminhtml\Product\Initialization\Helper\Plugin\HandlerInterface',
        'Magento\Catalog\Controller\Adminhtml\Product\Initialization\Helper\HandlerInterface'
    ),
    array(
        'Magento\PricePermissions\Controller\Adminhtml\Product\Initialization\Helper\Plugin\Handler\ProductType\Configurable',
        'Magento\ConfigurableProduct\Controller\Adminhtml\Product\Initialization\Helper\Plugin\Handler\ProductType\Configurable'
    ),
    array('Magento\FullPageCache\App\*'),
    array('Magento\FullPageCache\Block\*'),
    array('Magento\FullPageCache\Controller\Request'),
    array('Magento\FullPageCache\Helper\*'),
    array('Magento\FullPageCache\Model\*'),
    ['Magento\JobQueue'], // unused library code which was removed
    array('Magento\SalesArchive\Block\Adminhtml\Sales\Order\View\Tab\Shipments'),
    array('Magento\SalesArchive\Block\Adminhtml\Sales\Order\View\Tab\Invoices'),
    array('Magento\SalesArchive\Block\Adminhtml\Sales\Order\View\Tab\Creditmemos'),
    array('Magento\SalesArchive\Block\Adminhtml\Sales\Archive\Order\Creditmemo\Grid'),
    array('Magento\SalesArchive\Block\Adminhtml\Sales\Archive\Order\Invoice\Grid'),
    array('Magento\SalesArchive\Block\Adminhtml\Sales\Archive\Order\Shipment\Grid'),
    array('Magento\TargetRule\Model\Resource\Setup'),
    array('Magento\Enterprise\Model\Resource\Setup\Migration'),
);
