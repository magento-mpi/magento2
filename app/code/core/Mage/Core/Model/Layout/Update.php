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


class Mage_Core_Model_Layout_Update extends Varien_Simplexml_Config
{
    protected $_packageLayout;
    /**
     * Get Layout Updates Cache Object
     *
     * @return Zend_Cache_Core
     */
    public function getCache()
    {
        if (!$this->_cache) {
            $this->_cache = Zend_Cache::factory('Core', 'File', array(), array(
                'cache_dir'=>Mage::getBaseDir('cache_layout')
            ));
        }
        return $this->_cache;
    }

    public function load($handle)
    {
        $this->setCacheId($handle);
        if ($this->loadCache()) {
            return $this;
        }

        $this->_packageLayout = simplexml_load_file(Mage::getSingleton('design/package')->getLayoutFilename('core.xml'), $this->_elementClass);
        if (!$this->_packageLayout) {
            throw Mage::exception('Mage_Core', __('Could not load default layout file'));
        }
        if (!$this->_packageLayout->$handle) {
            throw Mage::exception('Mage_Core', __('Layout update handle not found: %s', $handle));
        }

        $this->mergeUpdate($this->_packageLayout->$handle);

        $this->saveCache();
        return $this;
    }

    /**
     * Enter description here...
     *
     * @param Mage_Core_Model_Layout_Element $update
     * @return Mage_Core_Model_Layout_Update
     */
    public function mergeUpdate(Mage_Core_Model_Layout_Element $updateXml)
    {
        foreach ($updateXml->children() as $child) {
            switch ($child->getName()) {
                case 'update':
                    $handle = (string)$child['handle'];
                    $this->mergeUpdate($this->_packageLayout->$handle);
                    break;

                case 'remove':
                    if (isset($child['method'])) {
                        $this->removeAction((string)$child['name'], (string)$child['method']);
                    } else {
                        $this->removeBlock((string)$child['name']);
                    }
                    break;

                default:
                    $this->getNode()->appendChild($child);
            }
        }
        return $this;
    }
}