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
require_once ('./Acl/FileWriter.php');
require_once ('./Acl/Formatter.php');

$shortOpts = 'ph';
$options = getopt($shortOpts);
try {
    $tool = new Tools_Migration_Acl_Generator(
        new Tools_Migration_Acl_Formatter(),
        new Tools_Migration_Acl_FileWriter(), $options
    );
    $tool->run();
} catch (Exception $exp) {
    echo $exp->getMessage();
}

