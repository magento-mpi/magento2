<?php
/**
 * {license_notice}
 *
 * @category   Tools
 * @package    acl
 * @copyright  {copyright}
 * @license    {license_link}
 */

require_once('./Acl/Formatter.php');
require_once('./Acl/FileManager.php');
require_once('./Acl/Generator.php');

$shortOpts = 'ph';
$options = getopt($shortOpts);
try {
    $tool = new \Magento\Tools\Migration\Acl\Generator(
        new \Magento\Tools\Migration\Acl\Formatter(),
        new \Magento\Tools\Migration\Acl\FileManager(), $options
    );
    $tool->run();
} catch (\Exception $exp) {
    echo $exp->getMessage();
}

