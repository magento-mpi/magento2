<?php
/**
 * @copyright Copyright (c) 2014 X.commerce, Inc. (http://www.magentocommerce.com)
 */

namespace Magento\Tools\SampleData\Module\Wishlist;

use Magento\Tools\SampleData\Helper\PostInstaller;
use Magento\Tools\SampleData\SetupInterface;

/**
 * Launches setup of sample data for Wishlist module
 */
class Setup implements SetupInterface
{
    /**
     * @var PostInstaller
     */
    protected $postInstaller;

    /**
     * @var Setup\Wishlist
     */
    protected $wishlistSetup;

    /**
     * @param PostInstaller $postInstaller
     * @param Setup\Wishlist $wishlistSetup
     */
    public function __construct(
        PostInstaller $postInstaller,
        Setup\Wishlist $wishlistSetup
    ) {
        $this->postInstaller = $postInstaller;
        $this->wishlistSetup = $wishlistSetup;
    }

    /**
     * {@inheritdoc}
     */
    public function run()
    {
        $this->postInstaller->addSetupResource($this->wishlistSetup);
    }
}
