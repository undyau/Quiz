INSERT INTO `user`(`name`, `password`) VALUES ("Joe Bloggs",SHA2("Joe Bloggspassword",256));

INSERT INTO question(number, answer) VALUES (1,"A");
INSERT INTO question(number, answer) VALUES (2,"B");

INSERT INTO admin(name, password) VALUES ("admin", SHA2("adminsomesecret",256));