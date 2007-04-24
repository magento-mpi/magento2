alter table `sales_quote_attribute_datetime` add index (`quote_id`, `entity_type`, `attribute_code`);
alter table `sales_quote_attribute_decimal` add index (`quote_id`, `entity_type`, `attribute_code`);
alter table `sales_quote_attribute_int` add index (`quote_id`, `entity_type`, `attribute_code`);
alter table `sales_quote_attribute_varchar` add index (`quote_id`, `entity_type`, `attribute_code`);

alter table `sales_order_attribute_datetime` add index (`order_id`, `entity_type`, `attribute_code`);
alter table `sales_order_attribute_decimal` add index (`order_id`, `entity_type`, `attribute_code`);
alter table `sales_order_attribute_int` add index (`order_id`, `entity_type`, `attribute_code`);
alter table `sales_order_attribute_varchar` add index (`order_id`, `entity_type`, `attribute_code`);

alter table `sales_invoice_attribute_datetime` add index (`invoice_id`, `entity_type`, `attribute_code`);
alter table `sales_invoice_attribute_decimal` add index (`invoice_id`, `entity_type`, `attribute_code`);
alter table `sales_invoice_attribute_int` add index (`invoice_id`, `entity_type`, `attribute_code`);
alter table `sales_invoice_attribute_varchar` add index (`invoice_id`, `entity_type`, `attribute_code`);