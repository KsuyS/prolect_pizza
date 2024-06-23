use php_project;
CREATE TABLE menu
(
    `pizza_id`      INT          NOT NULL AUTO_INCREMENT,
    `name`          VARCHAR(255) NOT NULL,
    `description`   VARCHAR(255) DEFAULT NULL,
    `price`         DECIMAL(6,2) NOT NULL,
    `image`         VARCHAR(255) DEFAULT NULL,
    PRIMARY KEY (`pizza_id`),
    UNIQUE INDEX `name_idx` (`name`)
);
