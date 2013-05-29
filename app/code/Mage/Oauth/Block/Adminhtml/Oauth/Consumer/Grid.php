<?php
/**
 * {license_notice}
 *
 * @category    Mage
 * @package     Mage_Adminhtml
 * @copyright  {copyright}
 * @license    {license_link}
 */

/**
 * OAuth Consumer grid block
 *
 * @category   Mage
 * @package    Mage_Oauth
 * @author     Magento Core Team <core@magentocommerce.com>
 */
class Mage_Oauth_Block_Adminhtml_Oauth_Consumer_Grid extends Mage_Adminhtml_Block_Widget_Grid
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
            ->setDefaultDir(Varien_Db_Select::SQL_DESC);

        $this->_editAllow = Mage::getSingleton('Mage_Core_Model_Authorization')->isAllowed('Mage_Oauth::consumer_edit');
    }

    /**
     * Prepare collection
     *
     * @return Mage_Oauth_Block_Adminhtml_Oauth_Consumer_Grid
     */
    protected function _prepareCollection()
    {
        $collection = Mage::getModel('Mage_Oauth_Model_Consumer')->getCollection();
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
            'header' => Mage::helper('Mage_Oauth_Helper_Data')->__('ID'), 'index' => 'entity_id', 'align' => 'right', 'width' => '50px'
        ));

        $this->addColumn('name', array(
            'header' => Mage::helper('Mage_Oauth_Helper_Data')->__('Consumer Name'), 'index' => 'name', 'escape' => true
        ));

        $this->addColumn('created_at', array(
            'header' => Mage::helper('Mage_Oauth_Helper_Data')->__('Created'), 'index' => 'created_at'
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
