<?php
$conn->query("
update `catalog_product_visibility` set visibility_code='Nowhere' where visibility_id=1
");
$conn->query("
update `catalog_product_visibility` set visibility_code='Catalog' where visibility_id=2
");
$conn->query("
update `catalog_product_visibility` set visibility_code='Search' where visibility_id=3
");
$conn->query("
update `catalog_product_visibility` set visibility_code='Catalog, Search' where visibility_id=4
");
