/*
*   select all users first/last name who made a purchase of products from "Office" category in "Rite Aid" shop for last 10 years
*/
SELECT DISTINCT u.user_first_name, u.user_last_name FROM orders o
    INNER JOIN users u ON o.id_user = u.id
    INNER JOIN shops sh ON o.id_shop = sh.id 
    INNER JOIN order_products o_p ON o.id_order = o_p.id_order
    INNER JOIN product_category p_c ON o_p.id_product = p_c.id_category
    INNER JOIN categoriesCatalog c_c ON p_c.id_category = c_c.id                                        
WHERE sh.shop_name='Rite Aid' 
    AND c_c.name='Office' 
    AND o.date >  DATE_SUB(CURDATE() ,INTERVAL 10 YEAR)
ORDER BY u.user_last_name

/*
*select names of all categories and count the number of purchases of products from that category
*/
SELECT DISTINCT (c_c.name), COUNT(DISTINCT o.id) AS orders FROM orders o
    JOIN order_products o_p ON o.id_order = o_p.id_order
    JOIN product_category p_c ON o_p.id_product = p_c.id_product
    JOIN categoriesCatalog c_c ON p_c.id_category = c_c.id
GROUP BY c_c.name
ORDER BY orders DESC

/*
*select users first/last name who have more then one purchase in "Kroger" shop
*/
SELECT  tabl.user_first_name, tabl.user_last_name FROM
(SELECT u.id, u.user_first_name, u.user_last_name FROM orders o
    INNER JOIN users u ON o.id_user = u.id
    INNER JOIN shops sh ON o.id_shop = sh.id                                       
WHERE sh.shop_name='Kroger') tabl
GROUP BY tabl.id
HAVING COUNT(tabl.id) > 1

/*
*show profit amount per month by particular shop (Might be useful in reporting)
*/
SELECT YEAR(o.date) AS Year, MONTHNAME(o.date) AS Months, SUM(o.sum) AS profit FROM orders o
    INNER JOIN shops sh ON o.id_shop = sh.id
WHERE sh.shop_name = 'Chase' AND YEAR(o.date) = 2013
GROUP BY Months

/*
*show amount of all purchases made by a user
*/
/*One user*/
SELECT u.user_first_name, u.user_last_name, SUM(o.sum) FROM orders o
    INNER JOIN users u ON o.id_user = u.id
WHERE u.user_email = 'homenick.humberto@hotmail.com'

/*All user*/
SELECT u.user_first_name, u.user_last_name, SUM(o.sum) FROM orders o
    INNER JOIN users u ON o.id_user = u.id
GROUP BY u.id


/*
*select users first/last name who have purchases purchases in all shops
*/
SELECT u.* FROM orders o
    INNER JOIN users u ON o.id_user = u.id
    INNER JOIN shops sh ON o.id_shop = sh.id
GROUP BY o.id_user
HAVING COUNT(DISTINCT sh.shop_name) = (SELECT COUNT(*) FROM shops)