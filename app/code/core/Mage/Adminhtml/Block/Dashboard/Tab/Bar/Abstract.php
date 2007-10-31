<?php
/**
 * Magento
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://opensource.org/licenses/osl-3.0.php
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@magentocommerce.com so we can send you a copy immediately.
 *
 * @category   Mage
 * @package    Mage_Adminhtml
 * @copyright  Copyright (c) 2004-2007 Irubin Consulting Inc. DBA Varien (http://www.varien.com)
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/**
 * Adminhtml dashboard tab bar abstract
 *
 * @category   Mage
 * @package    Mage_Adminhtml
 * @author      Ivan Chepurnyi <mitch@varien.com>
 */
 abstract class Mage_Adminhtml_Block_Dashboard_Tab_Bar_Abstract extends Mage_Adminhtml_Block_Widget
 {
    protected $_tabs;

    protected $_dataHelperName = null;

    public function __construct()
    {
        parent::__construct();
        $this->setTemplate('dashboard/tab/bar.phtml');
    }

    public function addTab($tabId, $type, array $options=array())
    {
        $tab = $this->getTabByType($type);
        $tab->addData($options);
        $tab->setType($type);
        $tab->setId($tabId);
        $this->_tabs[] = $tab;
        $this->setChild($tabId, $tab);

        return $tab;
    }

    public function getTab($tabId)
    {
        return $this->getChild($tabId);
    }

    public function getTabs()
    {
        return $this->_tabs;
    }

    protected function _prepareData()
    {
        return $this;
    }

    protected function _configureTabs()
    {
        if($this->getDataHelperName()) {
            foreach ($this->getTabs() as $tab) {
                if(!$tab->getDataHelperName()) {
                    $tab->setDataHelperName($this->getDataHelperName());
                }
            }
        }

        return $this;
    }


    public  function getDataHelperName()
    {
           return $this->_dataHelperName;
    }

    public  function setDataHelperName($dataHelperName)
    {
           $this->_dataHelperName = $dataHelperName;
           return $this;
    }

    protected function _initTabs()
    {
        return $this;
    }

    protected function _prepareLayout()
    {
        $this->_prepareData()
            ->_initTabs()
            ->_configureTabs();
        return parent::_prepareLayout();
    }

    public function getTabByType($type)
    {
        $block = '';
        switch ($type) {
            case "graph":
                $block = 'adminhtml/dashboard_tab_graph';
                break;

            case "grid":
            default:
                $block = 'adminhtml/dashboard_tab_grid';
                break;
        }

        return $this->getLayout()->createBlock($block);
    }

    public function getTabClassName($tab)
    {
        return  $tab->getType()=='graph' ? 'graph' : 'tab';
    }

    public function getCollection()
    {
        return $this->getDataHelper()->getCollection();
    }

    public function getDataHelper()
    {
        return $this->helper($this->getDataHelperName());
    }

    public function getCountTabs()
    {
        return sizeof($this->_tabs);
    }
 } // Class Mage_Adminhtml_Block_Dashboard_Tab_Bar_Abstract end