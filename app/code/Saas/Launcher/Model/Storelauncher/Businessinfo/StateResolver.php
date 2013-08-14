<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Saas_Launcher
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * State resolver for BusinessInfo Tile
 *
 * @category   Magento
 * @package    Saas_Launcher
 * @author     Magento Core Team <core@magentocommerce.com>
 */
class Saas_Launcher_Model_Storelauncher_Businessinfo_StateResolver
    extends Saas_Launcher_Model_Tile_ConfigBased_StateResolverAbstract
{
    /**
     * Default email address
     */
    const DEFAULT_EMAIL_ADDRESS = 'owner@example.com';

    /**
     * Constructor
     *
     * @param Magento_Core_Model_App $app
     */
    public function __construct(Magento_Core_Model_App $app)
    {
        parent::__construct($app);
        $this->_sections = array('trans_email');
    }

    /**
     * Resolve state
     *
     * @return bool
     */
    public function isTileComplete()
    {
        $email = $this->_app->getStore()->getConfig('trans_email/ident_general/email');
        return isset($email) && ($email != self::DEFAULT_EMAIL_ADDRESS);
    }
}
