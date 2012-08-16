<?php
/**
 * {license_notice}
 *
 * @category    Enterprise
 * @package     Enterprise_Staging
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Staging History Item View
 *
 *
 * @author     Magento Core Team <core@magentocommerce.com>
 */
class Enterprise_Staging_Block_Adminhtml_Log_View extends Mage_Adminhtml_Block_Widget_Form_Container
{
    protected $_informationRenderers = array();

    protected function _construct()
    {
        parent::_construct();
        $this->_blockGroup = 'Enterprise_Staging';
        $this->_controller = 'adminhtml_log';
        $this->_mode = 'view';

        $this->_headerText = Mage::helper('Enterprise_Staging_Helper_Data')->__('Details');
        $this->_removeButton('save');
        $this->_removeButton('delete');
        $this->_removeButton('reset');
    }

    public function getHeaderCssClass() {
        return 'icon-head head-staging-log';
    }

    public function addInformationRenderer($type, $block, $template)
    {
        $this->_informationRenderers[$type] = array(
            'block'     => $block,
            'template'  => $template,
            'renderer'  => null
        );
        return $this;
    }

    /**
     * Retrieve information renderer block
     *
     * @param string $type
     * @return Mage_Core_Block_Abstract
     */
    public function getInformationRenderer($type)
    {
        if (!isset($this->_informationRenderers[$type])) {
            $type = 'default';
        }
        if (is_null($this->_informationRenderers[$type]['renderer'])) {
            $this->_informationRenderers[$type]['renderer'] = $this->getLayout()
                ->createBlock($this->_informationRenderers[$type]['block'])
                ->setTemplate($this->_informationRenderers[$type]['template']);
        }
        return $this->_informationRenderers[$type]['renderer'];
    }

    public function getInformationHtml(Varien_Object $log)
    {
        return $this->getInformationRenderer($log->getAction())
            ->setLog($log)
            ->toHtml();
    }

    /**
     * Retrieve currently viewing log
     *
     * @return Enterprise_Staging_Model_Staging_Log
     */
    public function getLog()
    {
        if (!($this->getData('log') instanceof Enterprise_Staging_Model_Staging_Log)) {
            $this->setData('log', Mage::registry('log'));
        }
        return $this->getData('log');
    }

}
