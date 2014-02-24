<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_SalesArchive
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\SalesArchive\Block\Adminhtml\Sales\Archive\Order\Creditmemo;

/**
 * Archive creditmemos grid block
 */
class Grid
    extends \Magento\Sales\Block\Adminhtml\Creditmemo\Grid
{
    /**
     * Core url
     *
     * @var \Magento\Core\Helper\Url
     */
    protected $_coreUrl = null;

    /**
     * @param \Magento\Backend\Block\Template\Context $context
     * @param \Magento\Backend\Helper\Data $backendHelper
     * @param \Magento\Sales\Model\Order\CreditmemoFactory $creditmemoFactory
     * @param \Magento\Sales\Model\Resource\Order\Creditmemo\Grid\CollectionFactory $collectionFactory
     * @param \Magento\Core\Helper\Url $coreUrl
     * @param array $data
     */
    public function __construct(
        \Magento\Backend\Block\Template\Context $context,
        \Magento\Backend\Helper\Data $backendHelper,
        \Magento\Sales\Model\Order\CreditmemoFactory $creditmemoFactory,
        \Magento\Sales\Model\Resource\Order\Creditmemo\Grid\CollectionFactory $collectionFactory,
        \Magento\Core\Helper\Url $coreUrl,
        array $data = array()
    ) {
        $this->_coreUrl = $coreUrl;
        parent::__construct($context, $backendHelper, $creditmemoFactory, $collectionFactory, $data);
    }

    /**
     * @return void
     */
    protected function _construct()
    {
        parent::_construct();
        $this->setUseAjax(true);
        $this->setId('sales_creditmemo_grid_archive');
    }

    /**
     * Retrieve collection class
     *
     * @return string
     */
    protected function _getCollectionClass()
    {
        return 'Magento\SalesArchive\Model\Resource\Order\Creditmemo\Collection';
    }

    /**
     * Retrieve grid url
     *
     * @return string
     */
    public function getGridUrl()
    {
         return $this->getUrl('adminhtml/*/creditmemosgrid', array('_current' => true));
    }

    /**
     * Retrieve grid export types
     *
     * @return array|false
     */
    public function getExportTypes()
    {
        if (!empty($this->_exportTypes)) {
            foreach ($this->_exportTypes as $exportType) {
                $url = $this->_coreUrl->removeRequestParam($exportType->getUrl(), 'action');
                $exportType->setUrl($this->_coreUrl->addRequestParam($url, array('action' => 'creditmemo')));
            }
            return $this->_exportTypes;
        }
        return false;
    }
}
