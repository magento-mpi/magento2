# TEMPLATES

templates = {}

defTemplate     = """/**
 * Magento
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://opensource.org/licenses/osl-3.0.php
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@magentocommerce.com so we can send you a copy immediately.
 *
 * @category   {{category}}
 * @package    {{package}}
 * @copyright  Copyright (c) 2004-2007 Irubin Consulting Inc. DBA Varien (http://www.varien.com)
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */
"""

templates['.js']     = """/**
 * Magento
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://opensource.org/licenses/osl-3.0.php
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@magentocommerce.com so we can send you a copy immediately.
 *
 * @copyright  Copyright (c) 2004-2007 Irubin Consulting Inc. DBA Varien (http://www.varien.com)
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */
"""

templates['.css']    = """/**
 * Magento
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://opensource.org/licenses/osl-3.0.php
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@magentocommerce.com so we can send you a copy immediately.
 *
 * @copyright  Copyright (c) 2004-2007 Irubin Consulting Inc. DBA Varien (http://www.varien.com)
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */
"""

templates['.phtml']  = '<?php\n' + defTemplate + '?>\n'
templates['.php']    = '<?php\n' + defTemplate
templates['.xml']    = '<?xml version="1.0"?>\n<!--\n' + defTemplate + '-->'

# SCAN DIRECTORIES
scanDirectories = {
            'app/code/core/Mage/' : ('Mage_*', 'Mage'),
            'app/design/' : ('Mage', 'design_default'),
            'lib/Varien/': ('Varien_*', 'default'),
            'js/adminhtml/' : ('Mage', 'default'),
            'js/lib/' : ('Mage', 'default'),
            'js/listmenu/' : ('Mage', 'default'),
            'js/magenta/': ('Mage', 'default'),
            'js/varien/': ('Mage', 'default'),
            'skin/' : ('Mage', 'default'),
            'tests/' : ('Tests_*','default'),
        }
