<?php
/**
 * {license_notice}
 *
 * @category    Mage
 * @package     Mage_Adminhtml
 * @copyright   {copyright}
 * @license     {license_link}
 */

class Mage_Adminhtml_Block_Dashboard extends Mage_Adminhtml_Block_Template
{
    protected $_locale;

    /**
     * Location of the "Enable Chart" config param
     */
    const XML_PATH_ENABLE_CHARTS = 'admin/dashboard/enable_charts';

    protected function _construct()
    {
        parent::_construct();
        $this->setTemplate('dashboard/index.phtml');

    }

    protected function _prepareLayout()
    {
        $this->setChild('lastOrders',
                $this->getLayout()->createBlock('Mage_Adminhtml_Block_Dashboard_Orders_Grid')
        );

        $this->setChild('totals',
                $this->getLayout()->createBlock('Mage_Adminhtml_Block_Dashboard_Totals')
        );

        $this->setChild('sales',
                $this->getLayout()->createBlock('Mage_Adminhtml_Block_Dashboard_Sales')
        );

        $this->setChild('lastSearches',
                $this->getLayout()->createBlock('Mage_Adminhtml_Block_Dashboard_Searches_Last')
        );

        $this->setChild('topSearches',
                $this->getLayout()->createBlock('Mage_Adminhtml_Block_Dashboard_Searches_Top')
        );

        if (Mage::getStoreConfig(self::XML_PATH_ENABLE_CHARTS)) {
            $block = $this->getLayout()->createBlock('Mage_Adminhtml_Block_Dashboard_Diagrams');
        } else {
            $block = $this->getLayout()->createBlock('Mage_Adminhtml_Block_Template')
                ->setTemplate('dashboard/graph/disabled.phtml')
                ->setConfigUrl($this->getUrl('adminhtml/system_config/edit', array('section'=>'admin')));
        }
        $this->setChild('diagrams', $block);

        $this->setChild('grids',
                $this->getLayout()->createBlock('Mage_Adminhtml_Block_Dashboard_Grids')
        );

        parent::_prepareLayout();
    }

    public function getSwitchUrl()
    {
        if ($url = $this->getData('switch_url')) {
            return $url;
        }
        return $this->getUrl('*/*/*', array('_current'=>true, 'period'=>null));
    }
}
