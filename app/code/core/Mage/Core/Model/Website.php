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
 * @package    Mage_Core
 * @copyright  Copyright (c) 2004-2007 Irubin Consulting Inc. DBA Varien (http://www.varien.com)
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/**
 * Store
 *
 * @category   Mage
 * @package    Mage_Core
 * @author     Dmitriy Soroka <dmitriy@varien.com>
 */
class Mage_Core_Model_Website extends Mage_Core_Model_Abstract
{
    protected $_configCache = array();

    public function _construct()
    {
        $this->_init('core/website');
    }

    public function load($id, $field=null)
    {
        if (!is_numeric($id) && is_null($field)) {
            $this->getResource()->load($this, $id, 'code');
            return $this;
        }
        return parent::load($id, $field);
    }

    public function loadConfig($code)
    {
        if (is_numeric($code)) {
            foreach (Mage::getConfig()->getNode('websites')->children() as $websiteCode=>$website) {
                if ((int)$website->system->website->id==$code) {
                    $code = $websiteCode;
                    break;
                }
            }
        } else {
            $store = Mage::getConfig()->getNode('websites/'.$code);
        }
        if (!empty($website)) {
            $this->setCode($code);
            $id = (int)$website->system->website->id;
            $this->setId($id)->setStoreId($id);
        }
        return $this;
    }

    /**
     * Get website config data
     *
     * @param string $path
     * @return mixed
     */
    public function getConfig($path) {
        if (!isset($this->_configCache[$path])) {

            $config = Mage::getConfig()->getNode('websites/'.$this->getCode().'/'.$path);
            if (!$config) {
            	return false;
                #throw Mage::exception('Mage_Core', __('Invalid websites configuration path: %s', $path));
            }
            if (!$config->children()) {
                $value = (string)$config;
            } else {
                $value = array();
                foreach ($config->children() as $k=>$v) {
                    $value[$k] = $v;
                }
            }
            $this->_configCache[$path] = $value;
        }
        return $this->_configCache[$path];
    }

    public function getStoreCodes()
    {
        $stores = Mage::getConfig()->getNode('stores')->children();
        $storeCodes = array();
        foreach ($stores as $storeCode=>$storeConfig) {
            if ($this->getCode()===$storeCode) {
                $storeCodes[] = $storeCode;
            }
        }
        return $storeCodes;
    }

    public function getStoreCollection()
    {
		return $this->_storesCollection = Mage::getResourceModel('core/store_collection')
			->addWebsiteFilter($this->getId());
    }

    public function getStoresIds($notEmpty=false)
    {
    	$ids = array();

    	foreach ($this->getStoreCollection()->getItems() as $item) {
    		$ids[] = $item->getId();
    	}

    	if(count($ids)== 0 && $notEmpty) {
    		$ids[] = 0;
    	}

    	return $ids;
    }
}
