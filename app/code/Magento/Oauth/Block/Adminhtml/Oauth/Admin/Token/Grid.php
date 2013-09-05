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
class Magento_Oauth_Block_Adminhtml_Oauth_Admin_Token_Grid extends Magento_Adminhtml_Block_Widget_Grid
{
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
            ->setDefaultDir(\Magento\DB\Select::SQL_DESC);
    }

    /**
     * Prepare collection
     *
     * @return Magento_Oauth_Block_Adminhtml_Oauth_Admin_Token_Grid
     */
    protected function _prepareCollection()
    {
        /** @var $user Magento_User_Model_User */
        $user = Mage::getSingleton('Magento_Backend_Model_Auth_Session')->getData('user');

        /** @var $collection Magento_Oauth_Model_Resource_Token_Collection */
        $collection = Mage::getModel('Magento_Oauth_Model_Token')->getCollection();
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
