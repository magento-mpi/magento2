<?php
/**
 * {license_notice}
 *
 * @category    Mage
 * @package     Mage_Tag
 * @copyright   {copyright}
 * @license     {license_link}
 */

require_once 'Mage/Rss/controllers/CatalogController.php';

/**
 * Tag rss catalog controller
 *
 * @category   Mage
 * @package    Mage_Tag
 * @author     Magento Core Team <core@magentocommerce.com>
 */
class Mage_Tag_Rss_CatalogController extends Mage_Rss_CatalogController
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
