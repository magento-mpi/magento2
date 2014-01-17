<?php
/**
 * {license_notice}
 *
 * @copyright {copyright}
 * @license   {license_link}
 */

namespace Magento\Mview\View;

interface ChangelogInterface
{
    const NAME_QUALIFIER = 'cl';

    /**
     * Retrieve changelog name
     *
     * @return string
     */
    public function getName();

    /**
     * Retrieve changelog column name
     *
     * @return mixed
     */
    public function getColumnName();
}
