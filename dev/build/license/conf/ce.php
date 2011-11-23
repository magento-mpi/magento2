<?php
/**
 * Configuration file used by licence-tool.php script to prepare  Magento Community Edition
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
        'php'   => 'OSL'
    ),
    'app/code/core' => array(
        'xml'   => 'AFL',
        'phtml' => 'AFL',
        'php'   => 'OSL',
        'css'   => 'AFL',
        'js'    => 'AFL',
        '_params' => array(
            'skipped' => array(
                'file' => array(
                    'app/code/core/Zend/Mime.php'
                )
            )
        )
    ),
    'app/design' => array(
        'xml'   => 'AFL',
        'phtml' => 'AFL',
        'css'   => 'AFL',
        'js'    => 'AFL'
    ),
    'app/etc' => array(
        'xml'   => 'AFL',
        '_params' => array(
            'skipped' => array(
                'file' => 'app/etc/local.xml'
            )
        )
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
        '_params' => array(
            'skipped' => array(
                'dir' => array(
                    'dev/build',
                    'dev/tests/integration/tmp',
                    'dev/tests/static/testsuite/Php',
                    'dev/tests/static/report'

                )
            )
        )
    ),
    'downloader' => array(
        'xml'   => 'AFL',
        'phtml' => 'AFL',
        'php'   => 'OSL',
        'css'   => 'AFL',
        'js'    => 'AFL',
        '_params' => array(
            'skipped' => array(
                'file' => array(
                    'downloader/js/prototype.js'
                )
            )
        )
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
        '_params' => array(
            'skipped' => array(
                'dir' => array(
                    'pub/js/calendar',
                    'pub/js/extjs',
                    'pub/js/firebug',
                    'pub/js/prototype',
                    'pub/js/tiny_mce',
                    'pub/js/flash',
                    'pub/js/jscolor',
                    'pub/js/scriptaculous'
                ),
                'file' => array(
                    'pub/js/lib/FABridge.js',
                    'pub/js/lib/boxover.js',
                    'pub/js/lib/ccard.js',
                    'pub/js/lib/ds-sleight.js'
                )
            )
        )
    ),
    'pub/media' => array(
        'xml'   => 'AFL',
        'css'   => 'AFL',
        'js'    => 'AFL'
    )
);
