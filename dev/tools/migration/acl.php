<?php
/**
 * {license_notice}
 *
 * @category   Tools
 * @package    acl
 * @copyright  {copyright}
 * @license    {license_link}
 */

require_once ('./Acl/Generator.php');

$shortOpts = 'ph';
$options = getopt($shortOpts);
try {
    $tool = new Tools_Migration_Acl_Generator($options);
    $tool->run();
} catch (Exception $exp) {
    echo $exp->getMessage();
}

