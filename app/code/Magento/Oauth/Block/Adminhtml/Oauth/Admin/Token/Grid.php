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
 * OAuth My Application grid block
 *
 * @category   Magento
 * @package    Magento_Oauth
 * @author     Magento Core Team <core@magentocommerce.com>
 */
class Magento_Oauth_Block_Adminhtml_Oauth_Admin_Token_Grid extends Magento_Backend_Block_Widget_Grid_Extended
{
    /**
     * Token factory
     *
     * @var Magento_Oauth_Model_TokenFactory
     */
    protected $_tokenFactory = null;

    /**
     * Auth session
     *
     * @var Magento_Backend_Model_Auth_Session
     */
    protected $_authSession = null;

    /**
     * Config Source Yes/No
     *
     * @var Magento_Backend_Model_Config_Source_Yesno
     */
    protected $_configSourceYesNo = null;

    /**
     * @param Magento_Core_Helper_Data $coreData
     * @param Magento_Backend_Block_Template_Context $context
     * @param Magento_Core_Model_StoreManagerInterface $storeManager
     * @param Magento_Core_Model_Url $urlModel
     * @param Magento_Oauth_Model_TokenFactory $tokenFactory
     * @param Magento_Backend_Model_Auth_Session $authSession
     * @param Magento_Backend_Model_Config_Source_Yesno $configSourceYesNo
     * @param array $data
     */
    public function __construct(
        Magento_Core_Helper_Data $coreData,
        Magento_Backend_Block_Template_Context $context,
        Magento_Core_Model_StoreManagerInterface $storeManager,
        Magento_Core_Model_Url $urlModel,
        Magento_Oauth_Model_TokenFactory $tokenFactory,
        Magento_Backend_Model_Auth_Session $authSession,
        Magento_Backend_Model_Config_Source_Yesno $configSourceYesNo,
        array $data = array()
    ) {
        parent::__construct($coreData, $context, $storeManager, $urlModel, $data);
        $this->_tokenFactory = $tokenFactory;
        $this->_authSession = $authSession;
        $this->_configSourceYesNo = $configSourceYesNo;
    }


    /**
     * Construct grid block
     */
    protected function _construct()
    {
        parent::_construct();
        $this->setId('adminTokenGrid');
        $this->setUseAjax(true);
        $this->setSaveParametersInSession(true);
        $this->setDefaultSort('entity_id')
            ->setDefaultDir(Magento_DB_Select::SQL_DESC);
    }

    /**
     * Prepare collection
     *
     * @return Magento_Oauth_Block_Adminhtml_Oauth_Admin_Token_Grid
     */
    protected function _prepareCollection()
    {
        /** @var $user Magento_User_Model_User */
        $user = $this->_authSession->getData('user');

        /** @var $collection Magento_Oauth_Model_Resource_Token_Collection */
        $collection = $this->_tokenFactory->create()->getCollection();
        $collection->joinConsumerAsApplication()
                ->addFilterByType(Magento_Oauth_Model_Token::TYPE_ACCESS)
                ->addFilterByAdminId($user->getId());
        $this->setCollection($collection);

        parent::_prepareCollection();
        return $this;
    }

    /**
     * Prepare columns
     *
     * @return Magento_Oauth_Block_Adminhtml_Oauth_Admin_Token_Grid
     */
    protected function _prepareColumns()
    {
        $this->addColumn('entity_id', array(
            'header'    => __('ID'),
            'index'     => 'entity_id',
            'align'     => 'right',
            'width'     => '50px',
        ));

        $this->addColumn('name', array(
            'header'    => __('Application'),
            'index'     => 'name',
            'escape'    => true,
        ));

        $this->addColumn('revoked', array(
            'header'    => __('Revoked'),
            'index'     => 'revoked',
            'width'     => '100px',
            'type'      => 'options',
            'options'   => $this->_configSourceYesNo->toArray(),
            'sortable'  => true,
        ));

        parent::_prepareColumns();
        return $this;
    }

    /**
     * Add mass-actions to grid
     *
     * @return Magento_Oauth_Block_Adminhtml_Oauth_Admin_Token_Grid
     */
    protected function _prepareMassaction()
    {
        $this->setMassactionIdField('id');
        $block = $this->getMassactionBlock();

        $block->setFormFieldName('items');
        $block->addItem('enable', array(
            'label' => __('Enable'),
            'url'   => $this->getUrl('*/*/revoke', array('status' => 0)),
        ));
        $block->addItem('revoke', array(
            'label' => __('Revoke'),
            'url'   => $this->getUrl('*/*/revoke', array('status' => 1)),
        ));
        $block->addItem('delete', array(
            'label' => __('Delete'),
            'url'   => $this->getUrl('*/*/delete'),
        ));

        return $this;
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
}
