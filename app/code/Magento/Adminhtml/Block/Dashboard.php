<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Adminhtml
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Adminhtml\Block;

class Dashboard extends \Magento\Adminhtml\Block\Template
{
    protected $_locale;

    /**
     * Location of the "Enable Chart" config param
     */
    const XML_PATH_ENABLE_CHARTS = 'admin/dashboard/enable_charts';

    protected $_template = 'dashboard/index.phtml';

    protected function _prepareLayout()
    {
        $this->addChild('lastOrders', 'Magento\Adminhtml\Block\Dashboard\Orders\Grid');

        $this->addChild('totals', 'Magento\Adminhtml\Block\Dashboard\Totals');

        $this->addChild('sales', 'Magento\Adminhtml\Block\Dashboard\Sales');

        $this->addChild('lastSearches', 'Magento\Adminhtml\Block\Dashboard\Searches\Last');

        $this->addChild('topSearches', 'Magento\Adminhtml\Block\Dashboard\Searches\Top');

        if ($this->_storeConfig->getConfig(self::XML_PATH_ENABLE_CHARTS)) {
            $block = $this->getLayout()->createBlock('Magento_Adminhtml_Block_Dashboard_Diagrams');
        } else {
            $block = $this->getLayout()->createBlock('Magento\Adminhtml\Block\Template')
                ->setTemplate('dashboard/graph/disabled.phtml')
                ->setConfigUrl($this->getUrl('adminhtml/system_config/edit', array('section'=>'admin')));
        }
        $this->setChild('diagrams', $block);

        $this->addChild('grids', 'Magento\Adminhtml\Block\Dashboard\Grids');

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
