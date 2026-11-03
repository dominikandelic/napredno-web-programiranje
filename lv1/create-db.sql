CREATE DATABASE radovi
CHARACTER SET utf8mb4
COLLATE utf8mb4_croatian_ci;

USE radovi;

CREATE TABLE diplomski_radovi (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nazivRada VARCHAR(255),
    tekstRada LONGTEXT,
    linkRada VARCHAR(500),
    oibTvrtke VARCHAR(11)
);