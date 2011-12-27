<?php
/**
 * Configuration file used by licence-tool.php script to prepare Magento Community Edition
 *
 * {license_notice}
 *
 * @category   build
 * @package    license
 * @copyright  {copyright}
 * @license    {license_link}
 */

return array(
    '' => array(
        '_params' => array(
            'recursive' => false
        ),
        'php'   => 'OSL'
    ),
    'app/code/core' => array(
        'xml'   => 'AFL',
        'phtml' => 'AFL',
        'php'   => 'OSL',
        'css'   => 'AFL',
        'js'    => 'AFL',
    ),
    'app/design' => array(
        'xml'   => 'AFL',
        'phtml' => 'AFL',
        'css'   => 'AFL',
        'js'    => 'AFL',
    ),
    'app/etc' => array(
        'xml'   => 'AFL',
    ),
    'app' => array(
        'php'   => 'OSL',
        '_params' => array(
            'recursive' => false
        ),
    ),
    'app/code/community/Phoenix' => array(
        'xml'   => 'Phoenix',
        'phtml' => 'Phoenix',
        'php'   => 'Phoenix',
        'css'   => 'Phoenix',
        'js'    => 'Phoenix'
    ),
    'app/code/community/Find' => array(
        'xml'   => 'AFL',
        'phtml' => 'AFL',
        'php'   => 'OSL',
        'css'   => 'AFL',
        'js'    => 'AFL'
    ),
    'dev' => array(
        'xml'   => 'AFL',
        'phtml' => 'AFL',
        'php'   => 'OSL',
        'css'   => 'AFL',
        'js'    => 'AFL',
    ),
    'downloader' => array(
        'xml'   => 'AFL',
        'phtml' => 'AFL',
        'php'   => 'OSL',
        'css'   => 'AFL',
        'js'    => 'AFL',
    ),
    'lib/Varien' => array(
        'php'   => 'OSL'
    ),
    'lib/Mage' => array(
        'php'   => 'OSL'
    ),
    'lib/Magento' => array(
        'php'   => 'OSL',
        'xml'   => 'AFL'
    ),
    'lib/flex' => array(
        'xml'   => 'AFL',
        'flex'  => 'AFL'
    ),
    'pub' => array(
        'php' => 'OSL',
        '_params' => array(
            'recursive' => false
        ),
    ),
    'pub/errors' => array(
        'xml'   => 'AFL',
        'phtml' => 'AFL',
        'php'   => 'OSL',
        'css'   => 'AFL',
        'js'    => 'AFL'
    ),
    'pub/js' => array(
        'xml'   => 'AFL',
        'php'   => 'OSL',
        'css'   => 'AFL',
        'js'    => 'AFL',
    ),
    'pub/media' => array(
        'xml'   => 'AFL',
        'css'   => 'AFL',
        'js'    => 'AFL'
    ),
    'shell' => array(
        'php' => 'OSL',
    ),
);
