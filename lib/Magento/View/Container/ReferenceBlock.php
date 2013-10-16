<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\View\Container;

use Magento\View\Container as ContainerInterface;

class ReferenceBlock extends Block implements ContainerInterface
{
    /**
     * Container type
     */
    const TYPE = 'referenceBlock';
}
