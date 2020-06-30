DROP DATABASE IF EXISTS fh_2020_scm4_S1810307013;
CREATE DATABASE fh_2020_scm4_S1810307013;
USE fh_2020_scm4_S1810307013;

CREATE TABLE shopping_list (
	id int(11) NOT NULL AUTO_INCREMENT,
	creator_id int(11) NOT NULL,
	volunteer_id int(11) NULL DEFAULT NULL,
	title varchar(255) NOT NULL,
	date_until timestamp NOT NULL,
	deleted_at timestamp NULL DEFAULT NULL,
	created_at timestamp NOT NULL,
	status ENUM('Done', 'Draft', 'In progress', 'Open'),
	total_price decimal(10,2) NULL DEFAULT NULL,
	PRIMARY KEY (id),
	KEY creator_id (creator_id)
) ENGINE=InnoDB AUTO_INCREMENT=1 CHARSET=utf8;;

CREATE TABLE articles (
	id int(11) NOT NULL AUTO_INCREMENT,
    shopping_list_id int(11) NOT NULL,
	title varchar(255) NOT NULL,
	price_limit decimal(10,2) NOT NULL,
    amount int(11) NOT NULL,
	deleted_at timestamp NULL DEFAULT NULL,
	is_done BOOL DEFAULT false,
	PRIMARY KEY (id),
	KEY shopping_list_id (shopping_list_id)
) ENGINE=InnoDB AUTO_INCREMENT=1 CHARSET=utf8;;

CREATE TABLE users (
	id int(11) NOT NULL AUTO_INCREMENT,
	user_name varchar(255) NOT NULL,
	password_hash char(40) NOT NULL,
	user_type ENUM('volunteer', 'helpseeker'),
    PRIMARY KEY (id),
	UNIQUE KEY user_name (user_name)
) ENGINE=InnoDB AUTO_INCREMENT=1 CHARSET=utf8;;

ALTER TABLE articles
ADD CONSTRAINT articles_ibfk_1 FOREIGN KEY (shopping_list_id) REFERENCES shopping_list (id) ON DELETE CASCADE ON UPDATE CASCADE;

ALTER TABLE shopping_list
ADD CONSTRAINT shopping_list_ibfk_1 FOREIGN KEY (creator_id) REFERENCES users (id) ON DELETE CASCADE ON UPDATE CASCADE;

INSERT INTO users VALUES (1, 'Hans_Peter', "49be7d7950828a3bb9a8c95087583bf38d145fb1", "helpseeker");
INSERT INTO users VALUES (2, 'SuperGuy95', "f1eb5ae999e1a87931f8bfca7b4bf3fa384fe955", "volunteer");
INSERT INTO users VALUES (3, 'VolunteerByHeart', "5d7ac0e64f40d12d6e445d1d3b220a42a50a538b", "volunteer");
INSERT INTO users VALUES (4, 'Traudi', "7b057d651b47f76e5b7686a276cdef7c5567a3c0", "helpseeker");
INSERT INTO shopping_list VALUES (1, 1, null, 'My first list', '2020-06-16 13:00:00', null, '2020-06-15 13:00:00', 'Open', null);
INSERT INTO shopping_list VALUES (2, 1, null, 'My second list', '2020-06-16 13:00:00', null, '2020-06-15 13:00:00', 'Draft', null);
INSERT INTO shopping_list VALUES (3, 1, 2, 'My third list', '2020-06-16 13:00:00', null, '2020-06-15 13:00:00', 'In Progress', null);
INSERT INTO articles VALUES (1, 1, 'Brot 1kg', 2.95, 1, null, false);
INSERT INTO articles VALUES (2, 1, 'Cornetto Heidelbeer', 5.65, 4, null, false);
INSERT INTO articles VALUES (3, 2, 'Milch', 6.20, 3, null, false);
INSERT INTO articles VALUES (4, 3, 'Milchschnitte', 5.10, 5, null, false);

CREATE USER IF NOT EXISTS 'fh_2020_scm4'@'localhost' IDENTIFIED BY 'fh_2020_scm4';