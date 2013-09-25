<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Adminhtml
 * @copyright  {copyright}
 * @license    {license_link}
 */

/**
 * OAuth Consumer grid block
 *
 * @category   Magento
 * @package    Magento_Oauth
 * @author     Magento Core Team <core@magentocommerce.com>
 */
class Magento_Oauth_Block_Adminhtml_Oauth_Consumer_Grid extends Magento_Backend_Block_Widget_Grid_Extended
{
    /**
     * Consumer factory
     *
     * @var Magento_Oauth_Model_ConsumerFactory
     */
    protected $_consumerFactory = null;

    /**
     * @param Magento_Core_Helper_Data $coreData
     * @param Magento_Backend_Block_Template_Context $context
     * @param Magento_Core_Model_StoreManagerInterface $storeManager
     * @param Magento_Core_Model_Url $urlModel
     * @param Magento_Oauth_Model_ConsumerFactory $consumerFactory
     * @param array $data
     */
    public function __construct(
        Magento_Core_Helper_Data $coreData,
        Magento_Backend_Block_Template_Context $context,
        Magento_Core_Model_StoreManagerInterface $storeManager,
        Magento_Core_Model_Url $urlModel,
        Magento_Oauth_Model_ConsumerFactory $consumerFactory,
        array $data = array()
    ) {
        parent::__construct($coreData, $context, $storeManager, $urlModel, $data);
        $this->_consumerFactory = $consumerFactory;
    }

    /**
     * Allow edit status
     *
     * @var bool
     */
    protected $_editAllow = false;

    /**
     * Construct grid block
     */
    protected function _construct()
    {
        parent::_construct();
        $this->setId('consumerGrid');
        $this->setUseAjax(true);
        $this->setSaveParametersInSession(true);
        $this->setDefaultSort('entity_id')
            ->setDefaultDir(Magento_DB_Select::SQL_DESC);

        $this->_editAllow = $this->_authorization->isAllowed('Magento_Oauth::consumer_edit');
    }

    /**
     * Prepare collection
     *
     * @return Magento_Oauth_Block_Adminhtml_Oauth_Consumer_Grid
     */
    protected function _prepareCollection()
    {
        $collection = $this->_consumerFactory->create()->getCollection();
        $this->setCollection($collection);

        return parent::_prepareCollection();
    }

    /**
     * Prepare columns
     *
     * @return Magento_Oauth_Block_Adminhtml_Oauth_Consumer_Grid
     */
    protected function _prepareColumns()
    {
        $this->addColumn('entity_id', array(
            'header' => __('ID'), 'index' => 'entity_id', 'align' => 'right', 'width' => '50px'
        ));

        $this->addColumn('name', array(
            'header' => __('Consumer Name'), 'index' => 'name', 'escape' => true
        ));

        $this->addColumn('created_at', array(
            'header' => __('Created'), 'index' => 'created_at'
        ));

        return parent::_prepareColumns();
    }

    /**
     * Get grid URL
     *
     * @return string
     */
    public function getGridUrl()
    {
        return $this->getUrl('*/*/grid', array('_current' => true));
    }

    /**
     * Get row URL
     *
     * @param Magento_Oauth_Model_Consumer $row
     * @return string|null
     */
    public function getRowUrl($row)
    {
        if ($this->_editAllow) {
            return $this->getUrl('*/*/edit', array('id' => $row->getId()));
        }
        return null;
    }
}
