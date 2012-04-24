<?php
/**
 * {license_notice}
 *
 * @category    Mage
 * @package     Mage_Adminhtml
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Adminhtml page breadcrumbs
 *
 * @category   Mage
 * @package    Mage_Adminhtml
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Mage_Adminhtml_Block_Widget_Breadcrumbs extends Mage_Adminhtml_Block_Template
{
    /**
     * breadcrumbs links
     *
     * @var array
     */
    protected $_links = array();

    public function __construct()
    {
        $this->setTemplate('widget/breadcrumbs.phtml');
        $this->addLink(Mage::helper('Mage_Adminhtml_Helper_Data')->__('Home'), Mage::helper('Mage_Adminhtml_Helper_Data')->__('Home'), $this->getUrl('*'));
    }

    public function addLink($label, $title=null, $url=null)
    {
        if (empty($title)) {
            $title = $label;
        }
        $this->_links[] = array(
            'label' => $label,
            'title' => $title,
            'url'   => $url
        );
        return $this;
    }

    protected function _beforeToHtml()
    {
        // TODO - Moved to Beta 2, no breadcrumbs displaying in Beta 1
        // $this->assign('links', $this->_links);
        return parent::_beforeToHtml();
    }
}
