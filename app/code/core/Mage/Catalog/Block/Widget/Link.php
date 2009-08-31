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
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade Magento to newer
 * versions in the future. If you wish to customize Magento for your
 * needs please refer to http://www.magentocommerce.com for more information.
 *
 * @category   Mage
 * @package    Mage_Catalog
 * @copyright  Copyright (c) 2008 Irubin Consulting Inc. DBA Varien (http://www.varien.com)
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/**
 * Widget to display catalog link
 *
 * @category   Mage
 * @package    Mage_Catalog
 * @author     Magento Core Team <core@magentocommerce.com>
 */

class Mage_Catalog_Block_Widget_Link
    extends Mage_Core_Block_Html_Link
    implements Mage_Cms_Block_Widget_Interface
{
    /**
     * Entity model name which must be used to retrieve entity specific data.
     * @var null|Mage_Catalog_Model_Resource_Eav_Mysql4_Abstract
     */
    protected $_entityResource = null;

    /**
     * Initialize block
     */
    protected function _construct()
    {
        parent::_construct();
        /*
         * Saving original data to make sure we
         * have it without any other data manipulations.
         */
        $this->setOrigData();
    }

    /**
     * Prepare url using passed id path.
     *
     * @return string
     */
    public function getHref()
    {
        $store = Mage::app()->getStore();
        /* @var $store Mage_Core_Model_Store */
        $href = "";
        if ($this->getOrigData('id_path')) {
            $urlRewriteResource = Mage::getResourceSingleton('core/url_rewrite');
            /* @var $urlRewriteResource Mage_Core_Model_Mysql4_Url_Rewrite */
            $href = $urlRewriteResource->getRequestPathByIdPath($this->getOrigData('id_path'), $store);
        }

        return $store->getUrl('', array('_direct' => $href));
    }

    /**
     * Prepare anchor text using passed text as parameter.
     * If anchor text was not specified get entity name from DB.
     *
     * @return string
     */
    public function getAnchorText()
    {
        if (!$this->hasData('anchor_text') && $this->_entityResource) {
            $idPath = explode('/', $this->_getData('id_path'));
            $id = array_pop($idPath);
            if ($id) {
                $name = $this->_entityResource->getAttributeRawValue($id, 'name', Mage::app()->getStore());
                $this->setData('anchor_text', $name);
            }
        }

        return $this->_getData('anchor_text');
    }
}
