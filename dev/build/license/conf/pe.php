<?php
/**
 * Configuration file used by licence-tool.php script to prepare  Magento Professional Edition
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://opensource.org/licenses/osl-3.0.php
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@magentocommerce.com so we can send you a copy immediately.
 *
 * @category   build
 * @package    license
 * @subpackage conf
 * @copyright  Copyright (c) 2011 Magento Inc. (http://www.magentocommerce.com)
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/**
 * @var $config array of specified paths and file types with appropriate licenses
 *
 */
$config = array(
    '' => array(
        '_params' => array(
            'recursive' => false
        ),
        'php'   => 'MCL'
    ),
    'app/code/core' => array(
        'xml'   => 'MCL',
        'phtml' => 'MCL',
        'php'   => 'MCL',
        'css'   => 'MCL',
        'js'    => 'MCL'
    ),
    'app/design' => array(
        'xml'   => 'MCL',
        'phtml' => 'MCL',
        'css'   => 'MCL',
        'js'    => 'MCL'
    ),
    'app/etc' => array(
        'xml'   => 'MCL'
    ),
    'app/Mage.php' => array(
        'php'   => 'MCL'
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
        'xml'   => 'MCL',
        'phtml' => 'MCL',
        'php'   => 'MCL',
        'css'   => 'MCL',
        'js'    => 'MCL'
    ),
    'lib/Varien' => array(
        'php'   => 'MCL'
    ),
    'lib/Magento' => array(
        'php'   => 'MCL',
        'xml'   => 'MCL'
    ),
    'pub/error' => array(
        'xml'   => 'MCL',
        'phtml' => 'MCL',
        'php'   => 'MCL',
        'css'   => 'MCL',
        'js'    => 'MCL'
    ),
    'pub/jslib' => array(
        'xml'   => 'MCL',
        'php'   => 'MCL',
        'css'   => 'MCL',
        'js'    => 'MCL'
    ),
    'pub/media' => array(
        'xml'   => 'MCL',
        'css'   => 'MCL',
        'js'    => 'MCL'
    )
);
