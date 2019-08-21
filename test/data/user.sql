create table user
(
  id integer
    constraint user_pk
      primary key autoincrement,
  fullName text,
  email text
);


INSERT INTO user VALUES ('1', 'Marco Pivetta', 'adf@sdfsd.com');
INSERT INTO user VALUES ('2', 'Marco Pivetta', 'adf@sdfsd.com');
INSERT INTO user VALUES ('3', 'Marco Pivetta', 'adf@sdfsd.com');
