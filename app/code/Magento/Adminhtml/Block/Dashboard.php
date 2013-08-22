<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Adminhtml
 * @copyright   {copyright}
 * @license     {license_link}
 */

class Magento_Adminhtml_Block_Dashboard extends Magento_Adminhtml_Block_Template
{
    protected $_locale;

    /**
     * Location of the "Enable Chart" config param
     */
    const XML_PATH_ENABLE_CHARTS = 'admin/dashboard/enable_charts';

    protected $_template = 'dashboard/index.phtml';

    protected function _prepareLayout()
    {
        $this->addChild('lastOrders', 'Magento_Adminhtml_Block_Dashboard_Orders_Grid');

        $this->addChild('totals', 'Magento_Adminhtml_Block_Dashboard_Totals');

        $this->addChild('sales', 'Magento_Adminhtml_Block_Dashboard_Sales');

        $this->addChild('lastSearches', 'Magento_Adminhtml_Block_Dashboard_Searches_Last');

        $this->addChild('topSearches', 'Magento_Adminhtml_Block_Dashboard_Searches_Top');

        if (Mage::getStoreConfig(self::XML_PATH_ENABLE_CHARTS)) {
            $block = $this->getLayout()->createBlock('Magento_Adminhtml_Block_Dashboard_Diagrams');
        } else {
            $block = $this->getLayout()->createBlock('Magento_Adminhtml_Block_Template')
                ->setTemplate('dashboard/graph/disabled.phtml')
                ->setConfigUrl($this->getUrl('adminhtml/system_config/edit', array('section'=>'admin')));
        }
        $this->setChild('diagrams', $block);

        $this->addChild('grids', 'Magento_Adminhtml_Block_Dashboard_Grids');

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
