<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Core
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\View;

/**
 * Data Provider Interface.
 *
 * @category   Magento
 * @package    Magento_View
 * @author     Magento Core Team <core@magentocommerce.com>
 */
interface DataProvider
{
    public function setContainer(Element $container);

    public function getData();
}
