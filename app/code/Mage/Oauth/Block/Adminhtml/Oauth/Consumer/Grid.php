<?php
/**
 * {license_notice}
 *
 * @copyright  {copyright}
 * @license    {license_link}
 */

/**
 * OAuth Consumer grid block
 *
 * @author Magento Core Team <core@magentocommerce.com>
 */
class Mage_Oauth_Block_Adminhtml_Oauth_Consumer_Grid extends Mage_Backend_Block_Widget_Grid_Extended
{
    /** @var Mage_Oauth_Model_Consumer_Factory  */
    private $_consumerFactory;

    /**
     * Allow edit status
     *
     * @var bool
     */
    protected $_editAllow = false;

    /**
     * Internal constructor. Override _construct(), not __construct().
     *
     * @param Mage_Oauth_Model_Consumer_Factory $consumerFactory
     * @param Mage_Backend_Block_Template_Context $context
     * @param Mage_Core_Model_StoreManagerInterface $storeManager
     * @param Mage_Core_Model_Url $urlModel
     * @param array $data
     */
    public function __construct(
        Mage_Oauth_Model_Consumer_Factory $consumerFactory,
        Mage_Backend_Block_Template_Context $context,
        Mage_Core_Model_StoreManagerInterface $storeManager,
        Mage_Core_Model_Url $urlModel,
        array $data = array()
    ) {
        parent::__construct($context, $storeManager, $urlModel, $data);
        $this->_consumerFactory = $consumerFactory;
    }

    /**
     * Internal constructor: override this in subclasses
     */
    public function _construct()
    {
        parent::_construct();
        $this->setId('consumerGrid');
        $this->setSaveParametersInSession(true);
        $this->setDefaultSort('entity_id')->setDefaultDir(Varien_Db_Select::SQL_DESC);
        $this->_editAllow = $this->_authorization->isAllowed('Mage_Oauth::consumer_edit');
    }

    /**
     * Prepare collection
     *
     * @return Mage_Backend_Block_Widget_Grid
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
     * @return Mage_Oauth_Block_Adminhtml_Oauth_Consumer_Grid
     */
    protected function _prepareColumns()
    {
        $this->addColumn('entity_id', array(
            'header' => $this->__('ID'),
            'index'  => 'entity_id',
            'align'  => 'right',
            'width'  => '50px'
        ));

        $this->addColumn('name', array(
            'header' => $this->__('Add-On Name'),
            'index'  => 'name',
            'escape' => true
        ));

        $this->addColumn('http_post_url', array(
            'header' => $this->__('Http Post URL'),
            'index'  => 'http_post_url',
        ));

        $this->addColumn('created_at', array(
            'header' => $this->__('Created'),
            'index'  => 'created_at'
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
     * @param Mage_Oauth_Model_Consumer $row
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
