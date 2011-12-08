<?php
/**
 * {license_notice}
 *
 * @category    Mage
 * @package     Mage_Rss
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Default rss helper
 *
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Mage_Rss_Helper_Catalog extends Mage_Core_Helper_Abstract
{

    public function getTagFeedUrl()
    {
        $url = '';
        if(Mage::getStoreConfig('rss/catalog/tag') && $this->_getRequest()->getParam('tagId')){
            $tagModel = Mage::getModel('Mage_Tag_Model_Tag')->load($this->_getRequest()->getParam('tagId'));
            if($tagModel && $tagModel->getId()){
                return Mage::getUrl('rss/catalog/tag', array('tagName' => urlencode($tagModel->getName())));
            }
        }
        return $url;
    }

}
