<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\View\Container;

use Magento\View\Container as ContainerInterface;

class ReferenceContainer extends Container implements ContainerInterface
{
    /**
     * Container type
     */
    const TYPE = 'referenceContainer';
}
