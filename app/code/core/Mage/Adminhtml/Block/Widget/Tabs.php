<?php
/**
 * Tabs block
 *
 * @package     Mage
 * @subpackage  Adminhtml
 * @copyright   Varien (c) 2007 (http://www.varien.com)
 * @license     http://www.opensource.org/licenses/osl-3.0.php
 * @author      Dmitriy Soroka <dmitriy@varien.com>
 */
class Mage_Adminhtml_Block_Widget_Tabs extends Mage_Adminhtml_Block_Widget
{
    /**
     * tabs structure
     *
     * @var array
     */
    protected $_tabs = array();

    /**
     * Active tab key
     *
     * @var string
     */
    protected $_activeTab = null;

    /**
     * Destination HTML element id
     *
     * @var string
     */
    protected $_destElementId = 'content';

    public function __construct()
    {
        parent::__construct();
        $this->setTemplate('widget/tabs.phtml');
    }

    /**
     * retrieve destination html element id
     *
     * @return string
     */
    public function getDestElementId()
    {
        return $this->_destElementId;
    }

    public function setDestElementId($elementId)
    {
        $this->_destElementId = $elementId;
        return $this;
    }

    /**
     * Add new tab
     *
     * @param   string $tabId
     * @param   array|Varien_Object $tab
     * @return  Mage_Adminhtml_Block_Widget_Tabs
     */
    public function addTab($tabId, $tab)
    {
        if (is_array($tab)) {
            $this->_tabs[$tabId] = new Varien_Object($tab);
        }
        elseif ($tab instanceof Varien_Object) {
        	$this->_tabs[$tabId] = $tab;
        }
        else {
            throw new Exception('Wrong tab configuration');
        }

        if (is_null($this->_tabs[$tabId]->getUrl())) {
            $this->_tabs[$tabId]->setUrl('#');
        }

        if (!$this->_tabs[$tabId]->getTitle()) {
            $this->_tabs[$tabId]->setTitle($this->_tabs[$tabId]->getLabel());
        }

        $this->_tabs[$tabId]->setId($tabId);

        if (is_null($this->_activeTab)) $this->_activeTab = $tabId;
        if (true === $this->_tabs[$tabId]->getActive()) $this->setActiveTab($tabId);

        return $this;
    }

    public function getTabId(Varien_Object $tab)
    {
        return $this->getId().'_'.$tab->getId();
    }

    public function getActiveTabId()
    {
        return $this->getTabId($this->_tabs[$this->_activeTab]);
    }

    /**
     * Set Active Tab
     *
     * @param string $tabId
     * @return Mage_Adminhtml_Block_Widget_Tabs
     */
    public function setActiveTab($tabId)
    {
        if (isset($this->_tabs[$tabId])) {
            $this->_activeTab = $tabId;
            if (!(is_null($this->_activeTab)) && ($tabId !== $this->_activeTab)) {
                foreach ($this->_tabs as $id => $tab) {
                    $tab->setActive($id === $tabId);
                }
            }
        }
        return $this;
    }

    protected function _beforeToHtml()
    {
        $this->assign('tabs', $this->_tabs);
        return $this;
    }
}
