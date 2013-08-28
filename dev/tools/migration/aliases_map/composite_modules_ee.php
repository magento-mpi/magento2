<?php
/**
 * {license_notice}
 *
 * @category   Magento
 * @package    Mage
 * @copyright  {copyright}
 * @license    {license_link}
 */

/**
 * List on composite module names for Magento EE
 */

require_once realpath(dirname(dirname(dirname(dirname(__DIR__)))))
    . '/app/code/Magento/Core/Model/Resource/Setup.php';
require_once realpath(dirname(dirname(dirname(dirname(__DIR__)))))
    . '/app/code/Magento/Core/Model/Resource/Setup/Migration.php';
require_once realpath(dirname(dirname(dirname(dirname(__DIR__)))))
    . '/app/code/Enterprise/Enterprise/Model/Resource/Setup/Migration.php';

return Enterprise_Enterprise_Model_Resource_Setup_Migration::getCompositeModules();
