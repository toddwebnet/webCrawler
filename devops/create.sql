
CREATE USER 'webcrawler'@'%' IDENTIFIED BY 'J98ZXCcpSvS5eu6mXDkhRAxTTsCZqdvj';
CREATE DATABASE IF NOT EXISTS `webcrawler`;
GRANT ALL PRIVILEGES ON `webcrawler`.* TO 'webcrawler'@'%';GRANT ALL PRIVILEGES ON `webcrawler\_%`.* TO 'webcrawler'@'%';
flush privileges;
