<?php
/**
 * {license_notice}
 *
 * @copyright {copyright}
 * @license   {license_link}
 */

$source = '../../../app/code/Enterprise/Logging/etc/logging.xml';
$xsl = 'logging.xslt';
$out = 'logging.xml';
echo shell_exec("java -jar saxon9he.jar -s:$source -xsl:$xsl -o:$out");