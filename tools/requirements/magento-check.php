<?php
/**
 * Magento
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
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade Magento to newer
 * versions in the future. If you wish to customize Magento for your
 * needs please refer to http://www.magentocommerce.com for more information.
 *
 * @category   tools
 * @package    requirements
 * @copyright  Copyright (c) 2008 Irubin Consulting Inc. DBA Varien (http://www.varien.com)
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

extension_check(array(
    'curl',
    'dom',
    'gd',
    'hash',
    'iconv',
    'mcrypt',
    'pcre',
    'pdo',
    'pdo_mysql',
    'simplexml'
));

function extension_check($extensions)
{
    $fail = '';
    $pass = '';

    if(version_compare(phpversion(), '5.2.0', '<')) {
        $fail .= '<li>You need<strong> PHP 5.2.0</strong> (or greater)</li>';
    }
    else {
        $pass .='<li>You have<strong> PHP 5.2.0</strong> (or greater)</li>';
    }

    if(!ini_get('safe_mode')) {
        $pass .='<li>Safe Mode is <strong>off</strong></li>';
        $mysqlVersion = @shell_exec('mysql -V');
        if (!$mysqlVersion) {
            $fail .= '<li>Unable to detect MySQL version</li>';
        }
        else {
            preg_match('/[0-9]\.[0-9]+\.[0-9]+/', $mysqlVersion, $version);
            if (version_compare($version[0], '4.1.20', '<')) {
                $fail .= '<li>You need<strong> MySQL 4.1.20</strong> (or greater)</li>';
            }
            else {
                $pass .='<li>You have<strong> MySQL 4.1.20</strong> (or greater)</li>';
            }
        }
    } else {
        $fail .= '<li>Safe Mode is <strong>on</strong></li>';
    }

    foreach($extensions as $extension) {
        if(!extension_loaded($extension)) {
            $fail .= '<li> You are missing the <strong>'.$extension.'</strong> extension</li>';
        }
        else{    $pass .= '<li>You have the <strong>'.$extension.'</strong> extension</li>';
        }
    }

    if($fail) {
        echo '<p><strong>Your server does not meet the following requirements in order to install Magento.</strong>';
        echo '<br>The following requirements failed, please contact your hosting provider in order to receive assistance with meeting the system requirements for Magento:';
        echo '<ul>'.$fail.'</ul></p>';
        echo 'The following requirements were successfully met:';
        echo '<ul>'.$pass.'</ul>';
    } else {
        echo '<p><strong>Congratulations!</strong> Your server meets the requirements for Magento.</p>';
        echo '<ul>'.$pass.'</ul>';

    }
}
?>