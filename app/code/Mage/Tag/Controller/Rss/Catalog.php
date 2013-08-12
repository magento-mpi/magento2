<?php
/**
 * {license_notice}
 *
 * @category    Mage
 * @package     Mage_Tag
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Tag rss catalog controller
 *
 * @category   Mage
 * @package    Mage_Tag
 * @author     Magento Core Team <core@magentocommerce.com>
 */
class Mage_Tag_Controller_Rss_Catalog extends Magento_Rss_Controller_Catalog
{
    /**
     * Tag rss action
     *
     * @return void
     */
    public function tagAction()
    {
        if (!$this->_isEnabled('tag')) {
            $this->_forward('nofeed', 'index', 'rss');
            return;
        }
        $tagName = urldecode($this->getRequest()->getParam('tagName'));
        /** @var $tagModel Mage_Tag_Model_Tag */
        $tagModel = Mage::getModel('Mage_Tag_Model_Tag');
        $tagModel->loadByName($tagName);
        if ($tagModel->getId() && $tagModel->getStatus() == $tagModel->getApprovedStatus()) {
            Mage::register('tag_model', $tagModel);
            $this->_render();
            return;
        }
        $this->_forward('nofeed', 'index', 'rss');
    }
}
