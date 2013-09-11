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
namespace Magento\Oauth\Block\Adminhtml\Oauth\Consumer;

class Grid extends \Magento\Adminhtml\Block\Widget\Grid
{
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
            ->setDefaultDir(\Magento\DB\Select::SQL_DESC);

        $this->_editAllow = $this->_authorization->isAllowed('Magento_Oauth::consumer_edit');
    }

    /**
     * Prepare collection
     *
     * @return \Magento\Oauth\Block\Adminhtml\Oauth\Consumer\Grid
     */
    protected function _prepareCollection()
    {
        $collection = \Mage::getModel('Magento\Oauth\Model\Consumer')->getCollection();
        $this->setCollection($collection);

        return parent::_prepareCollection();
    }

    /**
     * Prepare columns
     *
     * @return \Magento\Oauth\Block\Adminhtml\Oauth\Consumer\Grid
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
     * @param \Magento\Oauth\Model\Consumer $row
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
