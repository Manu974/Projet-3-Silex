create database if not exists blog character set utf8 collate utf8_unicode_ci;
use blog;

grant all privileges on blog.* to 'blog_user'@'localhost' identified by 'Ovnir@$';
