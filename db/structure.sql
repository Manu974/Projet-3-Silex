
drop table if exists t_billet;
drop table if exists t_comment;
drop table if exists t_user;

create table t_user (
	user_id integer not null primary key auto_increment,
	user_name varchar(50) not null,
	user_password varchar(88) not null,
	user_salt varchar(23) not null,
	user_role varchar(50) not null
) engine=innodb character set utf8 collate utf8_unicode_ci;

create table t_billet (
    billet_id integer not null primary key auto_increment,
    billet_title varchar(100) not null,
    billet_content varchar(5000) not null,
    billet_publication datetime,
    author integer not null,
    constraint fk_billet_user foreign key(author) references t_user(user_id)
) engine=innodb character set utf8 collate utf8_unicode_ci;

create table t_comment (
	com_id integer not null primary key auto_increment,
	com_pseudo integer not null,
	com_dateofpost datetime not null,
	com_content varchar(200),
	billet_id integer not null,
	status boolean default null,
	report boolean default null,
	parent integer default null,
	level integer default null,
    constraint fk_com_billet foreign key(billet_id) references t_billet(billet_id),
    constraint fk_com_user foreign key(com_pseudo) references t_user(user_id)
) engine=innodb character set utf8 collate utf8_unicode_ci;
