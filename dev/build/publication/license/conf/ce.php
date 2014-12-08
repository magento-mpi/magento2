<?php
/**
 * Configuration file used by licence-tool.php script to prepare Magento Community Edition
 *
 * {license_notice}
 *
 * @copyright  {copyright}
 * @license    {license_link}
 */

$magentoOslAfl = [
    'xml'   => 'AFL',
    'phtml' => 'AFL',
    'php'   => 'OSL',
    'css'   => 'AFL',
    'js'    => 'AFL',
    'less'  => 'AFL',
];
$magentoAfl = $magentoOslAfl;
unset($magentoAfl['php']);

$config = [
    ''    => ['php' => 'OSL', '_recursive' => false],
    'app' => ['php' => 'OSL', '_recursive' => false],
    'app/code/Magento'      => $magentoOslAfl,
    'app/code/Magento/Ui'   => ['html' => 'AFL'],
    'app/design'            => $magentoAfl,
    'app/etc'               => ['xml' => 'AFL', 'php' => 'OSL'],
    'app/i18n'              => ['xml' => 'AFL'],
    'dev'                   => array_merge($magentoOslAfl, ['sql' => 'OSL', 'html' => 'AFL']),
    'lib/internal/flex'     => ['xml' => 'AFL', 'flex' => 'AFL'],
    'lib/internal/Magento'  => $magentoOslAfl,
    'lib/web'               => $magentoOslAfl,
    'pub'                   => $magentoOslAfl,
    'setup'                 => $magentoOslAfl,
];

if (defined('EDITION_LICENSE')) {
    foreach ($config as $path => $settings) {
        foreach ($settings as $type => $license) {
            if ('_params' == $type) {
                continue;
            }
            if ('OSL' == $license || 'AFL' == $license) {
                $config[$path][$type] = EDITION_LICENSE;
            }
        }
    }
}

return $config;
