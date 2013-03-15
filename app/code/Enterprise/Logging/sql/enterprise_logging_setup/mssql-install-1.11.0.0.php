<?php
/**
 * {license_notice}
 *
 * @category    Enterprise
 * @package     Enterprise_Logging
 * @copyright   {copyright}
 * @license     {license_link}
 */

/* @var $installer Mage_Core_Model_Resource_Setup */
$installer = $this;

$installer->getConnection()->query("
CREATE FUNCTION dbo.inet_ntoa
(
    @ip BIGINT
)
RETURNS CHAR(15) AS
BEGIN
    RETURN
        CONVERT(VARCHAR(4), FLOOR(@ip/256/256/256) % 256) + '.' +
        CONVERT(VARCHAR(4), FLOOR(@ip/256/256) % 256) + '.' +
        CONVERT(VARCHAR(4), FLOOR(@ip/256) % 256) + '.' +
        CONVERT(VARCHAR(4), @ip % 256)
END;
");

$installFile = dirname(__FILE__) . DS . 'install-1.11.0.0.php';
if (file_exists($installFile)) {
    include $installFile;
}
