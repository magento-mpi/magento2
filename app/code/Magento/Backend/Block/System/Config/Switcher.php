<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Backend
 * @copyright   {copyright}
 * @license     {license_link}
 */

class Magento_Backend_Block_System_Config_Switcher extends Magento_Backend_Block_Template
{
    /**
     * @return Magento_Core_Block_Abstract
     */
    protected function _prepareLayout()
    {
        $this->setTemplate('system/config/switcher.phtml');
        return parent::_prepareLayout();
    }

    /**
     * Retrieve list of available stores
     *
     * @return array
     */
    public function getStoreSelectOptions()
    {
        $section = $this->getRequest()->getParam('section');
        $curWebsite = $this->getRequest()->getParam('website');
        $curStore   = $this->getRequest()->getParam('store');

        $storeModel = Mage::getSingleton('Magento_Core_Model_System_Store');
        /* @var $storeModel Magento_Core_Model_System_Store */

        $options = array();
        $options['default'] = array(
            'label'    => __('Default Config'),
            'url'      => $this->getUrl('*/*/*', array('section' => $section)),
            'selected' => !$curWebsite && !$curStore,
            'style'    => 'background:#ccc; font-weight:bold;',
        );

        foreach ($storeModel->getWebsiteCollection() as $website) {
            $options = $this->_processWebsite($storeModel, $website, $section, $curStore, $curWebsite, $options);
        }

        return $options;
    }

    /**
     * Process website info
     *
     * @param Magento_Core_Model_System_Store $storeModel
     * @param Magento_Core_Model_Website $website
     * @param string $section
     * @param string $curStore
     * @param string $curWebsite
     * @param array $options
     * @return array
     */
    protected function _processWebsite(
        Magento_Core_Model_System_Store $storeModel,
        Magento_Core_Model_Website $website,
        $section,
        $curStore,
        $curWebsite,
        array $options
    ) {
        $websiteShow = false;
        foreach ($storeModel->getGroupCollection() as $group) {
            if ($group->getWebsiteId() != $website->getId()) {
                continue;
            }
            $groupShow = false;
            foreach ($storeModel->getStoreCollection() as $store) {
                if ($store->getGroupId() != $group->getId()) {
                    continue;
                }
                if (!$websiteShow) {
                    $websiteShow = true;
                    $options['website_' . $website->getCode()] = array(
                        'label' => $website->getName(),
                        'url' => $this->getUrl('*/*/*',
                            array('section' => $section, 'website' => $website->getCode())
                        ),
                        'selected' => !$curStore && $curWebsite == $website->getCode(),
                        'style' => 'padding-left:16px; background:#DDD; font-weight:bold;',
                    );
                }
                if (!$groupShow) {
                    $groupShow = true;
                    $options['group_' . $group->getId() . '_open'] = array(
                        'is_group' => true,
                        'is_close' => false,
                        'label' => $group->getName(),
                        'style' => 'padding-left:32px;'
                    );
                }
                $options['store_' . $store->getCode()] = array(
                    'label' => $store->getName(),
                    'url' => $this->getUrl('*/*/*',
                        array('section' => $section, 'website' => $website->getCode(), 'store' => $store->getCode())
                    ),
                    'selected' => $curStore == $store->getCode(),
                    'style' => '',
                );
            }
            if ($groupShow) {
                $options['group_' . $group->getId() . '_close'] = array(
                    'is_group' => true,
                    'is_close' => true,
                );
            }
        }
        return $options;
    }

    /**
     * Return store switcher hint html
     *
     * @return mixed
     */
    public function getHintHtml()
    {
        return Mage::getBlockSingleton('Magento_Backend_Block_Store_Switcher')->getHintHtml();
    }

    /**
     * Prepare html output
     *
     * @return string
     */
    protected function _toHtml()
    {
        if (!Mage::app()->isSingleStoreMode()) {
            return parent::_toHtml();
        }
        return '';
    }
}
