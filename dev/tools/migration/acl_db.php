<?php
/**
 * {license_notice}
 *
 * @category   Tools
 * @package    acl_db
 * @copyright  {copyright}
 * @license    {license_link}
 */

require_once ('./Acl/FileReader.php');

$map = Tools_Migration_Acl_FileReader::getInstance()->getAclIdentifiersMap();
var_dump($map);