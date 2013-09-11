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
namespace Magento\Oauth\Block\Adminhtml\Oauth\Admin\Token;

class Grid extends \Magento\Adminhtml\Block\Widget\Grid
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
     * @return \Magento\Oauth\Block\Adminhtml\Oauth\Admin\Token\Grid
     */
    protected function _prepareCollection()
    {
        /** @var $user \Magento\User\Model\User */
        $user = \Mage::getSingleton('Magento\Backend\Model\Auth\Session')->getData('user');

        /** @var $collection \Magento\Oauth\Model\Resource\Token\Collection */
        $collection = \Mage::getModel('\Magento\Oauth\Model\Token')->getCollection();
        $collection->joinConsumerAsApplication()
                ->addFilterByType(\Magento\Oauth\Model\Token::TYPE_ACCESS)
                ->addFilterByAdminId($user->getId());
        $this->setCollection($collection);

        parent::_prepareCollection();
        return $this;
    }

    /**
     * Prepare columns
     *
     * @return \Magento\Oauth\Block\Adminhtml\Oauth\Admin\Token\Grid
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

        /** @var $sourceYesNo \Magento\Backend\Model\Config\Source\Yesno */
        $sourceYesNo = \Mage::getSingleton('Magento\Backend\Model\Config\Source\Yesno');
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
     * @return \Magento\Oauth\Block\Adminhtml\Oauth\Admin\Token\Grid
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
