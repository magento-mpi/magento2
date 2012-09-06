<?php
/**
 * {license_notice}
 *
 * @category   Mage
 * @package    Mage
 * @copyright  {copyright}
 * @license    {license_link}
 */

/**
 * List on composite module names for Magento CE
 */

require_once realpath(dirname(dirname(dirname(dirname(__DIR__)))))
    . '/app/code/core/Mage/Core/Model/Resource/Setup.php';
require_once realpath(dirname(dirname(dirname(dirname(__DIR__)))))
    . '/app/code/core/Mage/Core/Model/Resource/Setup/Migration.php';

return Mage_Core_Model_Resource_Setup_Migration::getCompositeModules();
