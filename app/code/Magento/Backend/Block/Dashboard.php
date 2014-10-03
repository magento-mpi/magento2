<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Backend\Block;

class Dashboard extends \Magento\Backend\Block\Template
{
    /**
     * Location of the "Enable Chart" config param
     */
    const XML_PATH_ENABLE_CHARTS = 'admin/dashboard/enable_charts';

    /**
     * @var string
     */
    protected $_template = 'dashboard/index.phtml';

    /**
     * @return void
     */
    protected function _prepareLayout()
    {
        $this->addChild('lastOrders', 'Magento\Backend\Block\Dashboard\Orders\Grid');

        $this->addChild('totals', 'Magento\Backend\Block\Dashboard\Totals');

        $this->addChild('sales', 'Magento\Backend\Block\Dashboard\Sales');

        if ($this->_scopeConfig->getValue(self::XML_PATH_ENABLE_CHARTS, \Magento\Store\Model\ScopeInterface::SCOPE_STORE)) {
            $block = $this->getLayout()->createBlock('Magento\Backend\Block\Dashboard\Diagrams');
        } else {
            $block = $this->getLayout()->createBlock(
                'Magento\Backend\Block\Template'
            )->setTemplate(
                'dashboard/graph/disabled.phtml'
            )->setConfigUrl(
                $this->getUrl('adminhtml/system_config/edit', array('section' => 'admin'))
            );
        }
        $this->setChild('diagrams', $block);

        $this->addChild('grids', 'Magento\Backend\Block\Dashboard\Grids');

        parent::_prepareLayout();
    }

    /**
     * @return string
     */
    public function getSwitchUrl()
    {
        if ($url = $this->getData('switch_url')) {
            return $url;
        }
        return $this->getUrl('adminhtml/*/*', array('_current' => true, 'period' => null));
    }
}
