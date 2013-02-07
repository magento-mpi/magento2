<?php
/**
 * Object Manager config
 *
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
class Saas_Core_Model_ObjectManager_Config extends Mage_Core_Model_ObjectManager_Config
{
    /**
     * @param array $params
     */
    public function __construct($params)
    {
        parent::__construct($params);

        $this->_initialConfig['Saas_Saas_Model_Maintenance_Config'] = array(
            'parameters' => array(
                'config' => $this->_getParam('maintenance_mode'),
            ),
        );
    }
}
