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
 * OAuth authorized tokens grid block
 *
 * @category   Magento
 * @package    Magento_Oauth
 * @author     Magento Core Team <core@magentocommerce.com>
 */
class Magento_Oauth_Block_Adminhtml_Oauth_AuthorizedTokens_Grid extends Magento_Adminhtml_Block_Widget_Grid
{
    /**
     * Construct grid block
     */
    protected function _construct()
    {
        parent::_construct();
        $this->setId('authorizedTokensGrid');
        $this->setUseAjax(true);
        $this->setSaveParametersInSession(true);
        $this->setDefaultSort('entity_id')
            ->setDefaultDir(Magento_DB_Select::SQL_DESC);
    }

    /**
     * Prepare collection
     *
     * @return Magento_Oauth_Block_Adminhtml_Oauth_AuthorizedTokens_Grid
     */
    protected function _prepareCollection()
    {
        /** @var $collection Magento_Oauth_Model_Resource_Token_Collection */
        $collection = Mage::getModel('Magento_Oauth_Model_Token')->getCollection();
        $collection->joinConsumerAsApplication()
            ->addFilterByType(Magento_Oauth_Model_Token::TYPE_ACCESS);
        $this->setCollection($collection);

        parent::_prepareCollection();
        return $this;
    }

    /**
     * Prepare columns
     *
     * @return Magento_Oauth_Block_Adminhtml_Oauth_AuthorizedTokens_Grid
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

        $this->addColumn('type', array(
            'header'    => __('User Type'),
            //'index'     => array('customer_id', 'admin_id'),
            'options'   => array(0 => __('Admin'), 1 => __('Customer')),
            'frame_callback' => array($this, 'decorateUserType')
        ));

        $this->addColumn('user_id', array(
            'header'    => __('User ID'),
            //'index'     => array('customer_id', 'admin_id'),
            'frame_callback' => array($this, 'decorateUserId')
        ));

        /** @var $sourceYesNo Magento_Backend_Model_Config_Source_Yesno */
        $sourceYesNo = Mage::getSingleton('Magento_Backend_Model_Config_Source_Yesno');
        $this->addColumn('revoked', array(
            'header'    => __('Revoked'),
            'index'     => 'revoked',
            'width'     => '100px',
            'type'      => 'options',
            'options'   => $sourceYesNo->toArray(),
            'sortable'  => true,
        ));

        parent::_prepareColumns();
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

    /**
     * Get revoke URL
     *
     * @param Magento_Oauth_Model_Token $row
     * @return string|null
     */
    public function getRevokeUrl($row)
    {
        return $this->getUrl('*/*/revoke', array('id' => $row->getId()));
    }

    /**
     * Get delete URL
     *
     * @param Magento_Oauth_Model_Token $row
     * @return string|null
     */
    public function getDeleteUrl($row)
    {
        return $this->getUrl('*/*/delete', array('id' => $row->getId()));
    }

    /**
     * Add mass-actions to grid
     *
     * @return Magento_Oauth_Block_Adminhtml_Oauth_AuthorizedTokens_Grid
     */
    protected function _prepareMassaction()
    {
        if (!$this->_isAllowed()) {
            return $this;
        }

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
     * Decorate user type column
     *
     * @param string $value
     * @param Magento_Oauth_Model_Token $row
     * @param Magento_Adminhtml_Block_Widget_Grid_Column $column
     * @param bool $isExport
     * @return mixed
     */
    public function decorateUserType($value, $row, $column, $isExport)
    {
        $options = $column->getOptions();

        $value = ($row->getCustomerId())   ?$options[1]   :$options[0];
        $cell = $value;

        return $cell;
    }

    /**
     * Decorate user type column
     *
     * @param string $value
     * @param Magento_Oauth_Model_Token $row
     * @param Magento_Adminhtml_Block_Widget_Grid_Column $column
     * @param bool $isExport
     * @return mixed
     */
    public function decorateUserId($value, $row, $column, $isExport)
    {
        $value = ($row->getCustomerId())   ?$row->getCustomerId()   :$row->getAdminId();
        $cell = $value;

        return $cell;
    }

    /**
     * Check admin permissions
     *
     * @return boolean
     */
    protected function _isAllowed()
    {
        return $this->_authorization->isAllowed('Magento_Oauth::authorizedTokens');
    }
}
