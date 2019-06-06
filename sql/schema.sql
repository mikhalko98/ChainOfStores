CREATE TABLE shops(
    id INT AUTO_INCREMENT PRIMARY KEY,
    shop_name VARCHAR(30) NOT NULL,
    shop_domain VARCHAR(30) UNIQUE DEFAULT NULL
    );

CREATE TABLE users(
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_first_name VARCHAR(30) NOT NULL,
    user_last_name VARCHAR(30) NOT NULL,
    user_email VARCHAR(50) UNIQUE DEFAULT NULL
    );

CREATE TABLE productsCatalog(
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) UNIQUE NOT NULL
    );

CREATE TABLE categoriesCatalog(
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) UNIQUE NOT NULL
    );

CREATE TABLE order_products(
    id INT AUTO_INCREMENT PRIMARY KEY,
    id_order INT NOT NULL,
    id_product INT NOT NULL,
    FOREIGN KEY (id_product) REFERENCES productsCatalog(id),
    UNIQUE(id_order,id_product)
    );

CREATE TABLE product_category(
    id INT NOT NULL AUTO_INCREMENT PRIMARY KEY,
    id_product INT NOT NULL,
    id_category INT NOT NULL,
    FOREIGN KEY (id_product) REFERENCES productsCatalog(id),
    FOREIGN KEY (id_category) REFERENCES categoriesCatalog(id),
    CONSTRAINT UC_product_category UNIQUE(id_product,id_category)
);
CREATE INDEX id_order_index_xxx000 ON order_products(id_order);

CREATE TABLE orders(
    id INT NOT NULL AUTO_INCREMENT PRIMARY KEY,
    id_shop INT NOT NULL,
    id_user INT NOT NULL,
    sum INT,
    date DATETIME,
    id_order INT NOT NULL,
    FOREIGN KEY (id_shop) REFERENCES shops(id),
    FOREIGN KEY (id_user) REFERENCES users(id),
    FOREIGN KEY (id_order) REFERENCES order_products(id_order)
);