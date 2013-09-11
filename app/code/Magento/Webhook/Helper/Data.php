<?php
/**
 * Webhook module helper needed for translation.
 *
 * As long as we have code like \Magento\Backend\Model\Menu\Item that calls \Mage::helper() for every module
 * we will need every module to have a Data helper, even if the module itself doesn't use it thanks to
 * DI being available for translation.
 *
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Webhook\Helper;

class Data extends \Magento\Core\Helper\AbstractHelper
{
}
