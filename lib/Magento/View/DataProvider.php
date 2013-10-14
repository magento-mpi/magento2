<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Core
 * @copyright   {copyright}
 * @license     {license_link}
 */


/**
 * Data Provider Interface.
 *
 * @category   Magento
 * @package    Magento_View
 * @author     Magento Core Team <core@magentocommerce.com>
 */
interface Magento_View_DataProvider
{
    public function setContainer(Magento_View_Element $container);

    public function getData();
}
