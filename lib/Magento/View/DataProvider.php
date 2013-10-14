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
namespace Magento\View;

use Magento\View;

interface DataProvider
{
    public function setContainer(Element $container);

    public function getData();
}
