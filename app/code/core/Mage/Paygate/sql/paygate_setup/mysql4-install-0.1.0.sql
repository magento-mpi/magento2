drop table if exists paygate_authorizenet_debug;
create table paygate_authorizenet_debug (
debug_id int unsigned not null auto_increment primary key,
request_body text,
response_body text,
request_serialized text,
result_serialized text,
request_dump text,
result_dump text
);