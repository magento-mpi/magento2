<?php
/**
 * {license_notice}
 *
 * @category    Mage
 * @package     Saas_Launcher
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Hub controller
 *
 * @category    Mage
 * @package     Saas_Launcher
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Saas_Launcher_Controller_Adminhtml_Hub extends Magento_Backend_Controller_ActionAbstract
{
    /**
     * Launcher Helper
     *
     * @var Saas_Launcher_Helper_Data
     */
    protected  $_launcherHelper;

    /**
     * @param Magento_Backend_Controller_Context $context
     * @param Saas_Launcher_Helper_Data $launcherHelper
     * @param string $areaCode
     */
    public function __construct(
        Magento_Backend_Controller_Context $context,
        Saas_Launcher_Helper_Data $launcherHelper,
        $areaCode = null
    ) {
        parent::__construct($context, $areaCode);
        $this->_launcherHelper = $launcherHelper;
    }

    /**
     * Index action
     */
    public function indexAction()
    {
        if ($this->_launcherHelper->getLauncherPhase() == Saas_Launcher_Helper_Data::LAUNCHER_PHASE_PROMOTE_STORE) {
            $this->_redirect('*/promotestore_index/index');
        } else {
            $this->_redirect('*/storelauncher_index/index');
        }
    }
}
