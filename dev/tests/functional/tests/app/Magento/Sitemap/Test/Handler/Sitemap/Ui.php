<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Sitemap\Test\Handler\Sitemap; 

use Magento\Sitemap\Test\Handler\Sitemap\SitemapInterface;
use Mtf\Fixture\FixtureInterface;
use Mtf\Handler\Ui as AbstractUi;

/**
 * Class Ui
 *
 * @package Magento\Sitemap\Test\Handler\Sitemap
 */
class Ui extends AbstractUi implements SitemapInterface
{
   public function persist(FixtureInterface $fixture = null)
    {
        //
    }
}
