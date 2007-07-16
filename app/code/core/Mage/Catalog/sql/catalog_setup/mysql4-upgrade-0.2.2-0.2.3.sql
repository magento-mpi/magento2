drop table if exists catalog_search;
create table catalog_search (
search_id int unsigned not null auto_increment primary key,
search_query varchar(255) not null,
num_results int unsigned not null,
popularity int unsigned not null,
redirect varchar(255) not null,
key (search_query, popularity)
);
