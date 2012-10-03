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
     * @var Enterprise_SalesArchive_Model_Config $_authorization
     */
    protected $_authModel;

    /**
     * @param array $data
     */
    public function __construct($data = array())
    {
        $this->_authModel = (isset($data['authModel']))?
            $data['authModel'] : Mage::getSingleton('Mage_Core_Model_Authorization');

        parent::__construct($data);
    }

    /**
     * Generate row url
     * @param Varien_Object $item
     * @return bool|string
     */
    public function getUrl($item)
    {
        if ($this->_authModel->isAllowed('Enterprise_SalesArchive::orders')) {
            return parent::getUrl($item);
        }
        return false;
    }
}
