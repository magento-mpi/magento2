<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Backend
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * admin customer left menu
 *
 * @category   Magento
 * @package    Magento_Backend
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Magento_Backend_Block_System_Config_Dwstree extends Magento_Backend_Block_Widget_Tabs
{
    /**
     * @var Magento_Core_Model_StoreManagerInterface
     */
    protected $_storeManager;

    /**
     * @param Magento_Backend_Block_Template_Context $context
     * @param Magento_Core_Model_StoreManagerInterface $storeManager
     * @param array $data
     */
    public function __construct(
        Magento_Backend_Block_Template_Context $context,
        Magento_Core_Model_StoreManagerInterface $storeManager,
        array $data = array()
    ) {
        parent::__construct($context, $data);
        $this->_storeManager = $storeManager;
    }

    protected function _construct()
    {
        parent::_construct();
        $this->setId('system_config_dwstree');
        $this->setDestElementId('system_config_form');
    }

    /**
     * @return Magento_Backend_Block_System_Config_Dwstree
     */
    public function initTabs()
    {
        $section = $this->getRequest()->getParam('section');

        $curWebsite = $this->getRequest()->getParam('website');
        $curStore = $this->getRequest()->getParam('store');

        $this->addTab('default', array(
            'label'  => __('Default Config'),
            'url'    => $this->getUrl('*/*/*', array('section'=>$section)),
            'class' => 'default',
        ));

        /** @var $website Magento_Core_Model_Website */
        foreach ($this->_storeManager->getWebsites(true) as $website) {
            $wCode = $website->getCode();
            $wName = $website->getName();
            $wUrl = $this->getUrl('*/*/*', array('section' => $section, 'website' => $wCode));
            $this->addTab('website_' . $wCode, array(
                'label' => $wName,
                'url'   => $wUrl,
                'class' => 'website',
            ));
            if ($curWebsite === $wCode) {
                if ($curStore) {
                    $this->_addBreadcrumb($wName, '', $wUrl);
                } else {
                    $this->_addBreadcrumb($wName);
                }
            }
            /** @var $store Magento_Core_Model_Store */
            foreach ($website->getStores() as $store) {
                $sCode = $store->getCode();
                $sName = $store->getName();
                $this->addTab('store_' . $sCode, array(
                    'label' => $sName,
                    'url'   => $this->getUrl('*/*/*', array(
                        'section' => $section, 'website' => $wCode, 'store' => $sCode)
                    ),
                    'class' => 'store',
                ));
                if ($curStore === $sCode) {
                    $this->_addBreadcrumb($sName);
                }
            }
        }
        if ($curStore) {
            $this->setActiveTab('store_' . $curStore);
        } elseif ($curWebsite) {
            $this->setActiveTab('website_' . $curWebsite);
        } else {
            $this->setActiveTab('default');
        }

        return $this;
    }
}
