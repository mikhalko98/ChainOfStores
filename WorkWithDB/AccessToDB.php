<?php
require_once "WriteCSVfile.php";

class AccessToDB{
    private $pdo;
    private $WriteCVSfile;

    function __construct(PDO $pdo)
    {
        $this->pdo = $pdo;
    }
    function addPurchase(){
        $FillDBChainOfStores = new FillDB($this->pdo);
        $FillDBChainOfStores->readAndFill("../purch.json");
        echo "Successfully added\n";
    }
    function getArrIdTableInDB($sql, $uniqueColumn)
    {
        $row = $this->pdo->query($sql)->fetchAll(PDO::FETCH_ASSOC);
        $arrIdProducts = array();
        foreach ($row as $val)
            $arrIdProducts[$val[$uniqueColumn]] = $val['id'];
        return $arrIdProducts;
    }
    function checkShowName($shop_name){
        $sql = "SELECT id FROM shops WHERE shop_name = ?";
        $sth = $this->pdo->prepare($sql);
        $sth->execute(array($shop_name));
        $data = $sth->fetch(PDO::FETCH_ASSOC);
        if($data['id']) return true;
        else return false;
    }
    function checkCategoryName($category_name){
        $sql = "SELECT id FROM categoriesCatalog WHERE name = ?";
        $sth = $this->pdo->prepare($sql);
        $sth->execute(array($category_name));
        $data = $sth->fetch(PDO::FETCH_ASSOC);
        if($data['id']) return true;
        else return false;
    }
    function firstSelect(){
        echo "Enter shop name: ";
        $shop_name = trim(fgets(STDIN));
        if(!$this->checkShowName($shop_name)){
            echo "Incorrect shop name\n *exit in menu*\n";
            return;
        }
        echo  "Enter category name: ";
        $category_name = trim(fgets(STDIN));
        if(!$this->checkCategoryName($category_name)){
            echo "Incorrect category name\n *exit in menu*\n";
            return;
        }
        echo "Enter year:";
        $year = (int)trim(fgets(STDIN));
        $sql = "SELECT u.user_first_name, u.user_last_name, u.user_email, COUNT(DISTINCT o.id) AS countBuy FROM orders o
    INNER JOIN users u ON o.id_user = u.id
    INNER JOIN shops sh ON o.id_shop = sh.id 
    INNER JOIN order_products o_p ON o.id_order = o_p.id_order
    INNER JOIN product_category p_c ON o_p.id_product = p_c.id_category
    INNER JOIN categoriesCatalog c_c ON p_c.id_category = c_c.id                                        
WHERE sh.shop_name=? 
    AND c_c.name=? 
    AND o.date >  DATE_SUB(CURDATE() ,INTERVAL ? YEAR)
GROUP BY o.id_user
ORDER BY countBuy DESC";
        $sth = $this->pdo->prepare($sql);
        $sth->execute(array($shop_name, $category_name, $year));
        $data = $sth->fetchAll();
        $this->WriteCVSfile = new WriteCSVfile("first.csv");
        $check = false;
        $i=1;
        printf("%-4s%-15s%-15s%-40s%s\n","N",'First name','Last name','Email','Count buy');
        foreach ($data as $key => $value) {
           printf("%-4d%-15s%-15s%-40s%d\n",$i++,$value['user_first_name'],$value['user_last_name'],$value['user_email'],$value['countBuy']);
           $check = true;
           if($i>20) break;
        }
        if($check);
        else echo "*Data not found* \n";
    }
    function secondSelect(){
        $sql = "SELECT DISTINCT (c_c.name), COUNT(DISTINCT o.id) AS orders FROM orders o
    JOIN order_products o_p ON o.id_order = o_p.id_order
    JOIN product_category p_c ON o_p.id_product = p_c.id_product
    JOIN categoriesCatalog c_c ON p_c.id_category = c_c.id
GROUP BY c_c.name
ORDER BY orders DESC";
        $sth = $this->pdo->prepare($sql);
        $sth->execute();
        $data = $sth->fetchAll();
        $this->WriteCVSfile = new WriteCSVfile("second.csv");
        $check = false;
        printf("%-60s%s\n","Name category",'Count buy');
        foreach ($data as $key => $value) {
            printf("%-60s%d\n",$value['name'],$value['orders']);
            $check = true;
        }
        if($check);
        else echo "*Data not found* \n";
    }
    function thirdSelect(){
        echo "Enter shop name: ";
        $shop_name = trim(fgets(STDIN));
        if(!$this->checkShowName($shop_name)){
            echo "Incorrect shop name\n *exit in menu*\n";
            return;
        }
        echo "Enter count purchase: ";
        $n = (int)trim(fgets(STDIN));
        $sql = "SELECT  tabl.user_first_name, tabl.user_last_name, tabl.user_email, COUNT(tabl.user_email) AS countBuy, SUM(tabl.sum) as sumBuy FROM
(SELECT u.id, u.user_first_name, u.user_last_name, u.user_email, o.sum FROM orders o
    INNER JOIN users u ON o.id_user = u.id
    INNER JOIN shops sh ON o.id_shop = sh.id                                       
WHERE sh.shop_name=?) tabl
GROUP BY tabl.id
HAVING COUNT(tabl.id) > ?
ORDER BY sumBuy DESC";
        $sth = $this->pdo->prepare($sql);
        $sth->execute(array($shop_name,$n));
        $data = $sth->fetchAll();
        $this->WriteCVSfile = new WriteCSVfile("third.csv");
        $check = false;
        printf("%-4s%-15s%-15s%-40s%-10s%s\n","N",'First name','Last name','Email','Count buy','Sum buy');
        $i=1;
        foreach ($data as $key => $value) {
            printf("%-4d%-15s%-15s%-40s%-10s%s\n",$i++,$value['user_first_name'],$value['user_last_name'],$value['user_email'],$value['countBuy'],$value['sumBuy']);
            if($i>20) break;
            $check = true;
        }
        if($check);
        else echo "*Data not found* \n";
    }
    function fourthSelect(){
        echo "Enter shop name: ";
        $shop_name = trim(fgets(STDIN));
        if(!$this->checkShowName($shop_name)){
            echo "Incorrect shop name\n *exit in menu*\n";
            return;
        }
        echo "Enter year:";
        $year = (int)trim(fgets(STDIN));
        $sql = "SELECT YEAR(o.date) AS Year, MONTHNAME(o.date) AS Months, SUM(o.sum) AS profit FROM orders o
    INNER JOIN shops sh ON o.id_shop = sh.id
WHERE sh.shop_name = ? AND YEAR(o.date) = ?
GROUP BY Months";
        $sth = $this->pdo->prepare($sql);
        $sth->execute(array($shop_name,$year));
        $data = $sth->fetchAll();
        $this->WriteCVSfile = new WriteCSVfile("fourth.csv");
        $check = false;
        printf("%-6s%-15s%s\n",'Year','Month', 'Sum buys');
        $sum = 0;
        foreach ($data as $key => $value) {
            printf("%-6d%-15s%d\n",$value['Year'],$value['Months'], $value['profit']);
            $sum +=$value['profit'];
            $check = true;
        }
        if($check) echo "\nProfit for the $year: ",$sum,"\n";
        else echo "*Data not found* \n";
    }
    function fifthSelect(){
        echo "Enter user email: ";
        $user_email = trim(fgets(STDIN));
        $sql = "SELECT u.user_first_name, u.user_last_name,u.user_email,o.date,o.sum, pC.name FROM orders o
    INNER JOIN users u ON o.id_user = u.id
    INNER JOIN order_products o_p ON o_p.id_order = o.id_order
    INNER JOIN productsCatalog pC ON pC.id = o_p.id_product
WHERE u.user_email = ?";
        $sth = $this->pdo->prepare($sql);
        $sth->execute(array($user_email));
        $data = $sth->fetchAll();
        $this->WriteCVSfile = new WriteCSVfile("fifth.csv");
        $check = false;
        printf("%s%s %s; %s: %s\n",'Full name: ',$data[0]['user_first_name'],$data[0]['user_last_name'], 'Email',$data[0]['user_email']);
        $date0=$data[0]['date'];
        $sum = $data[0]['sum'];
        printf("     %s\n",$date0);
        printf("     %s\n","Products:");
        foreach ($data as $key => $value) {
            if($value['date']!==$date0){
                $date0=$value['date'];
                printf("     SUM = %s\n\n",$sum);
                printf("     %s\n",$value['date']);
                printf("     %s\n","Products:");
            }
            if($value['date']===$date0){
                printf("               %s\n",$value['name']);
                $check = true;
                $sum = $value['sum'];
            }
            else $sum = $value['sum'];

        }
        if($check) printf("     SUM = %s\n\n",$sum);
        else echo "*Data not found* \n";
    }
    function sixthSelect(){
        $sql = "SELECT u.* FROM orders o
    INNER JOIN users u ON o.id_user = u.id
    INNER JOIN shops sh ON o.id_shop = sh.id
GROUP BY o.id_user
HAVING COUNT(DISTINCT sh.shop_name) = (SELECT COUNT(*) FROM shops)";
        $sth = $this->pdo->prepare($sql);
        $sth->execute();
        $data = $sth->fetchAll();
        $this->WriteCVSfile = new WriteCSVfile("sixth.csv");
        $check = false;
        $i=1;
        printf("%-4s%-15s%-15s%-40s\n","N",'First name','Last name','Email');
        foreach ($data as $key => $value) {
            printf("%-4d%-15s%-15s%-40s\n",$i++,$value['user_first_name'],$value['user_last_name'],$value['user_email'],);
            if($i>20) break;
            $check = true;
        }
        if($check);
        else echo "*Data not found* \n";
    }
}
?>