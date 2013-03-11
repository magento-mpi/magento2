<?php
/**
 * {license_notice}
 *
 * @category    Enterprise
 * @package     Enterprise_SalesArchive
 * @copyright   {copyright}
 * @license     {license_link}
 */


/**
 * Sales Archive Grid row url generator
 *
 * @category    Enterprise
 * @package     Enterprise_SalesArchive
 * @author      Magento Core Team <core@magentocommerce.com>
 */

class Enterprise_SalesArchive_Model_Order_Archive_Grid_Row_UrlGenerator
    extends Mage_Backend_Model_Widget_Grid_Row_UrlGenerator
{
    /**
     * @var $_authorizationModel Mage_Core_Model_Authorization
     */
    protected $_authorizationModel;

    /**
     * @param array $args
     */
    public function __construct(array $args = array())
    {
        $this->_authorizationModel = isset($args['authorizationModel']) ?
            $args['authorizationModel'] : Mage::getSingleton('Mage_Core_Model_Authorization');

        parent::__construct($args);
    }

    /**
     * Generate row url
     * @param Varien_Object $item
     * @return bool|string
     */
    public function getUrl($item)
    {
        if ($this->_authorizationModel->isAllowed('Enterprise_SalesArchive::orders')) {
            return parent::getUrl($item);
        }
        return false;
    }
}
