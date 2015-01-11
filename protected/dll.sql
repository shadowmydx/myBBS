create table user (
	user_id integer auto_increment primary key,
	username varchar(200) not null,
	password varchar(200) not null
)auto_increment = 1;

grant all privileges on mybbs.* to bbsroot@"%" identified by '123';


create table subject (
	sub_id integer auto_increment primary key,
	name varchar(400) not null,
	pubdate timestamp default current_timestamp,
	user_id integer,
	totalpart integer, -- 标记该贴有几个回复
	foreign key (user_id) references user(user_id)
)auto_increment = 1;

create table issue (
	issue_id integer auto_increment primary key,
	sub_id integer,
	content text not null,
	pubdate timestamp default current_timestamp,
	user_id integer,
	foreign key (user_id) references user(user_id),
	foreign key (sub_id)  references subject(sub_id)
)auto_increment = 1;

create table content (
	sub_id integer,
	issue_id integer,
	whichpart integer,-- 第几楼
	primary key (sub_id,issue_id)
)auto_increment = 1;

create table totalsubject (
	totalsub integer primary key
);
insert into totalsubject(totalsub) values (0);
grant select,update,insert,delete on mybbs.subject to bbsuser@"%" identified by '123';
grant select,update on mybbs.totalsubject to bbsuser@"%" identified by '123';
grant select on mybbs.user to bbsuser@"%" identified by '123';
grant select,update,insert,delete on mybbs.issue to bbsuser@"%" identified by '123';
grant select,update,insert,delete on mybbs.content to bbsuser@"%" identified by '123';

grant select on mybbs.* to guest@"%" identified by '123';