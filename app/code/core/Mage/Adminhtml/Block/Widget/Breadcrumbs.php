<?php
/**
 * Adminhtml page breadcrumbs
 *
 * @package     Mage
 * @subpackage  Breadcrumbs
 * @copyright   Varien (c) 2007 (http://www.varien.com)
 * @license     http://www.opensource.org/licenses/osl-3.0.php
 * @author      Dmitriy Soroka <dmitriy@varien.com>
 */
class Mage_Adminhtml_Block_Widget_Breadcrumbs extends Mage_Core_Block_Template
{
    /**
     * breadcrumbs links
     *
     * @var array
     */
    protected $_links = array();

    public function __construct()
    {
        $this->setTemplate('adminhtml/widget/breadcrumbs.phtml');
        $this->addLink(__('Home'), __('Home Title'), Mage::getUrl('adminhtml'));
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
        $this->assign('links', $this->_links);
        return $this;
    }
}