<?php
/**
 * {license_notice}
 *
 * @category    Mage
 * @package     Mage_Install
 * @copyright   {copyright}
 * @license     {license_link}
 */


/**
 * Install index controller
 *
 * @category   Mage
 * @package    Mage_Install
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Mage_Install_IndexController extends Mage_Install_Controller_Action
{

    public function preDispatch()
    {
        $this->setFlag('', self::FLAG_NO_CHECK_INSTALLATION, true);
        return parent::preDispatch();
    }

    function indexAction()
    {
        $this->_forward('begin', 'wizard', 'install');
    }

}
