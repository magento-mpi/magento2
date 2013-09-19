<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
class Magento_Install_Model_Config_Reader extends Magento_Config_Reader_Filesystem
{
    /**
     * List of identifier attributes for merging
     *
     * @var array
     */
    protected $_idAttributes = array(
        '/install_wizard/steps/step' => 'id',
        '/install_wizard/filesystem_prerequisites/directory' => 'alias'
    );
}
