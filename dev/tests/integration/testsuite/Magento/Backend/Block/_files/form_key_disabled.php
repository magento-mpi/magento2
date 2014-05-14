<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

\Magento\TestFramework\Helper\Bootstrap::getObjectManager()->get(
    'Magento\Backend\Model\UrlInterface'
)->turnOffSecretKey();
