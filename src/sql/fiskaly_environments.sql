DELIMITER ;

create table if not exists fiskaly_environments (
    id varchar(255) not null primary key,
    type enum('live','test') not null default 'test',
    val longtext not null
);

/*
alter table fiskaly_environments
    add column if not exists type enum('live','test') not null default 'test';

-- change the primary key to include the type
alter table fiskaly_environments  
    drop primary key,
    add primary key (id, type); 

*/
/*
insert ignore into fiskaly_environments (id, val) values ('api_key', 'api_key');
insert ignore into fiskaly_environments (id, val) values ('api_secret', 'api_secret');
*/

create table if not exists fiskaly_tss (
    tss varchar(36) not null,
    id varchar(100) not null,
    primary key (tss, id),
    val longtext not null
);

create table kassenterminals_client_id (
  kassenterminal varchar(36) primary key,
  tss_client_id varchar(36) not null,
  constraint `fk_kassenterminals_client_id`
  foreign key (kassenterminal)
  references kassenterminals(id)
);