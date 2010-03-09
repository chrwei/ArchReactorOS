CREATE TABLE `banned_country` (
  `banned_country_id` int(11) NOT NULL auto_increment,
  `country_name` varchar(255) NOT NULL,
  `country_code` varchar(255) NOT NULL,
  PRIMARY KEY  (`banned_country_id`)
);

CREATE TABLE `banned_ip` (
  `banned_ip_id` int(11) NOT NULL auto_increment,
  `ip_address_start` varchar(255) NOT NULL,
  `ip_address_end` varchar(255) NOT NULL,
  `ip_number_start` int(11) NOT NULL,
  `ip_number_end` int(11) NOT NULL,
  PRIMARY KEY  (`banned_ip_id`)
);

CREATE TABLE `config` (
  `config_id` int(10) unsigned NOT NULL auto_increment,
  `name` varchar(255) default NULL,
  `value` varchar(255) default NULL,
  PRIMARY KEY  (`config_id`)
);

CREATE TABLE `email_templates` (
  `email_id` int(10) NOT NULL auto_increment,
  `name` varchar(30) NOT NULL default '',
  `subject` varchar(255) NOT NULL default '',
  `body` text NOT NULL,
  PRIMARY KEY  (`email_id`)
);

CREATE TABLE `orders` (
  `order_id` int(11) NOT NULL auto_increment,
  `user_id` int(11) NOT NULL,
  `product_id` int(11) NOT NULL,
  `date_order` int(11) NOT NULL,
  `date_expire` int(11) NOT NULL,
  `last_email_sent` int(11) NOT NULL,
  PRIMARY KEY  (`order_id`)
);

CREATE TABLE `payment` (
  `payment_id` int(11) NOT NULL auto_increment,
  `order_id` int(11) NOT NULL,
  `date_payment` int(11) NOT NULL,
  `amount` double NOT NULL,
  `currency_code` char(5) NOT NULL,
  `payment_gateway` varchar(255) NOT NULL,
  `invoice_id` varchar(255) NOT NULL,
  `log` text NOT NULL,
  PRIMARY KEY  (`payment_id`)
);

CREATE TABLE `product` (
  `product_id` int(11) NOT NULL auto_increment,
  `name` varchar(255) NOT NULL,
  `description` varchar(255) NOT NULL,
  `price` float NOT NULL,
  `duration` int(11) NOT NULL,
  `duration_unit` char(1) NOT NULL,
  `path` varchar(255) NOT NULL,
  `url` varchar(255) NOT NULL,
  PRIMARY KEY  (`product_id`)
);

CREATE TABLE `user` (
  `user_id` int(11) unsigned NOT NULL auto_increment,
  `username` varchar(255) NOT NULL,
  `password` varchar(255) NOT NULL,
  `firstname` varchar(255) NOT NULL,
  `lastname` varchar(255) NOT NULL,
  `email` varchar(255) NOT NULL,
  `street` varchar(255) NOT NULL,
  `city` varchar(255) NOT NULL,
  `state` varchar(255) NOT NULL,
  `country` varchar(255) NOT NULL,
  `phone` varchar(255) NOT NULL,
  `date` int(11) NOT NULL,
  `admin` int(11) NOT NULL,
  PRIMARY KEY  (`user_id`)
);

CREATE TABLE `currency` (
  `currency_id` int(11) NOT NULL auto_increment,
  `currency_code` char(5) NOT NULL,
  `currency_name` varchar(255) NOT NULL,
  `currency_pay_unit` int(11) default NULL,
  `currency_usage` tinyint(1) NOT NULL,
  PRIMARY KEY  (`currency_id`,`currency_code`)
);

CREATE TABLE `discount` (
  `discount_id` int(11) NOT NULL auto_increment,
  `product_id` int(11) NOT NULL,
  `coupon_id` int(11) NOT NULL,
  PRIMARY KEY  (`discount_id`,`product_id`,`coupon_id`)
);

CREATE TABLE `discount_coupon` (
  `coupon_id` int(11) NOT NULL auto_increment,
  `coupon_code` varchar(255) default NULL,
  `coupon_value` varchar(50) default NULL,
  `start_date` int(11) default NULL,
  `expire_date` int(11) default NULL,
  `expire_usage` int(11) default NULL,
  `usage_count` int(11) default NULL,
  PRIMARY KEY  (`coupon_id`)
);

CREATE TABLE `payment_gateway` (
  `payment_gateway_id` int(11) NOT NULL,
  `payment_gateway_name` varchar(255) NOT NULL,
  `payment_gateway_account` varchar(255) default NULL,
  `payment_gateway_status` tinyint(1) NOT NULL,
  PRIMARY KEY  (`payment_gateway_name`)
);

CREATE TABLE `invoice` (
  `invoice_id` varchar(10) NOT NULL default '',
  `invoice_date` int(11) default NULL,
  `due_date` int(11) default NULL,
  `invoiced_to` varchar(255) default NULL,
  `service` varchar(255) default NULL,
  `description` text ,
  `price` float default NULL,
  `discount_price` float default NULL,
  `total_price` float default NULL,
  `currency_code` char(5) default NULL,
  `comment` text ,
  `paid` char(1) default NULL,
  `paid_date` int(11) default NULL,
  `paid_gateway` varchar(255) default NULL,
  `email` varchar(255) default NULL,
  PRIMARY KEY  (`invoice_id`)
);

CREATE TABLE `invoice_config` (
  `company` varchar(255) default NULL,
  `contact` varchar(255) default NULL,
  `address` text,
  `phone` varchar(255) default NULL,
  `email` varchar(255) default NULL
);

TRUNCATE TABLE `config`;
TRUNCATE TABLE `invoice_config`;
TRUNCATE TABLE `email_templates`;
TRUNCATE TABLE `payment_gateway`;

INSERT INTO `payment_gateway` (`payment_gateway_id`, `payment_gateway_name`, `payment_gateway_account`, `payment_gateway_status`) VALUES
(1, 'paypal_payments', '', 0),
(2, 'paypal_subscribe', '', 0),
(3, '2co', '', 0),
(4, '2co_subscribe', '', 0),
(5, 'alertpay', '', 0),
(6, 'alertpay_subscribe', '', 0),
(7, 'moneybookers', '', 0);

INSERT INTO `invoice_config` (`company`, `contact`, `address`, `phone`, `email`) VALUES
('Your company name', 'Billing Department', 'Your company address', 'Your company phone', 'Your company email');

INSERT INTO `email_templates` (`email_id`, `name`, `subject`, `body`) VALUES
(1, 'request_password', 'Account information', 'Dear, %firstname%,\r\n\r\nHere is your account information for %site_name% membership :\r\n\r\nUsername   : %username%\r\nPasssword  : %password%\r\n\r\n%from_name%'),
(2, 'confirm_order', 'Account Order Complete', 'Dear, %username%,\r\n\r\nThank you for your account order from %site_name%.\r\nThe following purchase(s) were complete.\r\n\r\nfor account information\r\n\r\nFirstname		: %firstname%\r\nLast name		: %lastname%\r\nPasssword		: %password%\r\nMembership		: %product_name% \r\nDetail			: %product_desc% \r\nExpire			: %product_expire%\r\nTotal amount billed 	: %product_price%\r\nDownload area		: %url%\r\n\r\nWe are very glad you join as member. Your feedback will be very helpfull to us.\r\n\r\n%from_name%'),
(3, 'expire_notification', 'Account will expire', 'Dear, %username%\r\n\r\nWe inform you that your account %username% in %site_name% will expire in %date_expire%\r\n\r\nYour account information :\r\nMembership    : %product_name% \r\nDescription   : %product_desc% \r\nPrice         : %product_price%\r\nExpire        : %product_expire%\r\nDownload area : %url%\r\n\r\nThank you for joining with us.\r\n\r\n%from_name%'),
(4, 'received_new_order', 'Received new order', 'Dear Administrator\r\n\r\nCongratulation, you have received a new order at %date_order% form : \r\n\r\nFirstname  : %firstname%\r\nLastname   : %lastname%\r\nMembership : %product_name% \r\nDescription : %product_desc% \r\nPrice      : %product_price%\r\nExpire     : %product_expire%\r\nDownload area : %url%\r\n\r\n\r\n%from_name%'),
(5, 'account_expire', 'Account Is Expire', 'Dear, %username%\r\n\r\nWe inform you that your account in %site_name% has expired in %date_expire%\r\n\r\nYour account information :\r\n\r\nFirstname	: %firstname%\r\nLastname	: %lastname%\r\nMembership	: %product_name% \r\nDescription 	: %product_desc% \r\nPrice		: %product_price%\r\nExpire		: %product_expire%\r\nDownload area	: %url%\r\n\r\nif you wish to renew your account please visit %site_url%\r\n\r\nThank you for joining with us.\r\n\r\n%from_name%');

INSERT INTO `currency` (`currency_id`, `currency_code`, `currency_name`, `currency_pay_unit`, `currency_usage`) VALUES
(1, 'CAD', 'Canadian Dollars', 2, 0),
(2, 'EUR', 'Euros', 85, 0),
(3, 'GBP', 'Pounds Sterling', 44, 0),
(4, 'USD', 'U.S. Dollars', 1, 1),
(5, 'JPY', 'Yen', 81, 0),
(6, 'AUD', 'Australian Dollars', 61, 0),
(7, 'CHF', 'Swiss Francs', 41, 0);

INSERT INTO `user`(`user_id`,`username`,`password`,`firstname`,`lastname`,`email`,`street`,`city`,`state`,`country`,`phone`,`date`,`admin`) values (1,'admin','admin','Administrator','Administrator','','','','','','',0,1)