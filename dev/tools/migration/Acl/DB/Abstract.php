<?php
/**
 * {license_notice}
 *
 * @category   Magento
 * @package    tools
 * @copyright  {copyright}
 * @license    {license_link}
 */

abstract class Tools_Migration_Acl_Db_Logger_Abstract
{
    public function add($oldKey, $newKey, $updateResult)
    {

    }

    public function __toString()
    {

    }

    public abstract function report();
}
