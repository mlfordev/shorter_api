CREATE TABLE `admins`
(
    `id`         int(11) unsigned NOT NULL AUTO_INCREMENT,
    `login`      varchar(255) NOT NULL,
    `password`   varchar(255) NOT NULL,
    `created_at` datetime DEFAULT NULL,
    `updated_at` datetime DEFAULT NULL,
    PRIMARY KEY (`id`),
    UNIQUE KEY `unique_login` (`login`)
) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8;

INSERT INTO `admins` (`id`, `login`, `password`, `created_at`, `updated_at`)
VALUES (1, 'admin', '$2y$10$f5NuMP946EiD4qI2y8LV6.KRBkaT7sD2drstylpZUXrWfGd6E6emi', '2021-01-19 07:51:00',
        '2021-01-20 20:14:41');

CREATE TABLE `short_links`
(
    `id`         int(11) unsigned NOT NULL AUTO_INCREMENT,
    `url`        varchar(2083) NOT NULL DEFAULT '',
    `created_at` datetime               DEFAULT NULL,
    `updated_at` datetime               DEFAULT NULL,
    `position`   int(11) DEFAULT NULL,
    PRIMARY KEY (`id`),
    KEY          `full_url` (`url`(255))
) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8;

CREATE TABLE `clicks`
(
    `id`              int(11) unsigned NOT NULL AUTO_INCREMENT,
    `remote_addr`     varchar(255)  DEFAULT NULL,
    `remote_host`     varchar(255)  DEFAULT NULL,
    `http_user_agent` varchar(255)  DEFAULT NULL,
    `http_host`       varchar(255)  DEFAULT NULL,
    `http_referer`    varchar(2083) DEFAULT NULL,
    `short_link_id`   int(11) unsigned NOT NULL,
    `created_at`      datetime      DEFAULT NULL,
    `updated_at`      datetime      DEFAULT NULL,
    PRIMARY KEY (`id`),
    KEY               `clicks_short_links_fk` (`short_link_id`),
    CONSTRAINT `clicks_short_links_fk` FOREIGN KEY (`short_link_id`) REFERENCES `short_links` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=7 DEFAULT CHARSET=utf8;
