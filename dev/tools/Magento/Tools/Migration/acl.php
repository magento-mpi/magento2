<?php
/**
 * {license_notice}
 *
 * @category   Tools
 * @package    acl
 * @copyright  {copyright}
 * @license    {license_link}
 */

require_once('./Acl/Generator.php');
require_once('./Acl/FileManager.php');
require_once('./Acl/Formatter.php');

$shortOpts = 'ph';
$options = getopt($shortOpts);
try {
    $tool = new Magento_Tools_Migration_Acl_Generator(
        new Magento_Tools_Migration_Acl_Formatter(),
        new Magento_Tools_Migration_Acl_FileManager(), $options
    );
    $tool->run();
} catch (Exception $exp) {
    echo $exp->getMessage();
}

