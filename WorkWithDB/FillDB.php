<?php
require_once "lib/WriteToFile.php";
require_once "WorkWithDB/AccessToDB.php";

class FillDB
{
    private $pdo;
    private $WriteToFile;
    private $AccessToDB;
    private $id_Order = 0;
    private $count = 20000;

    function __construct(PDO $pdo)
    {
        $this->pdo = $pdo;
        $this->WriteToFile = new WriteToFile('./file.csv', './file.sql');
        $this->AccessToDB = new AccessToDB($this->pdo);
    }

    public function readAndFill($fileName)
    {
        $handle = fopen($fileName, "r");
        if ($handle) {
            while (!feof($handle)) {
                $shops = array();
                for ($i = 0; $i < $this->count; $i++) {
                    $shops[] = fgets($handle);
                    if (feof($handle)) break;
                }
                $arr = array();
                foreach ($shops as $value) {
                    $order = json_decode($value);
                    $arr[] = $order;
                }
                $this->fill($arr);
            }
        }
    }

    private function fill($order)
    {
        $this->addShops($order);
        $this->addUsers($order);
        $this->addProducts($order);
        $this->addCategories($order);
        $this->addProduct_category($order);
        $this->addOrder_products($order);
        $this->addOrders($order);
    }

    private function loadDateInfile($table)
    {
        $sql = "LOAD DATA INFILE '" . $this->WriteToFile->getNameCSV() . "' IGNORE INTO TABLE %s FIELDS TERMINATED BY ',' ENCLOSED BY '\"'";
        try {
            $this->pdo->exec(sprintf($sql, $table));
        } catch (PDOException $e) {
            echo $sql . "<br>" . $e->getMessage();
        }
        $this->WriteToFile->clearCSVFile();
    }

    private function insert()
    {
        try {
            $this->pdo->exec(file_get_contents($this->WriteToFile->getNameSQL()));
        } catch (PDOException $e) {
            echo $e->getMessage();
        }
        $this->WriteToFile->clearSQLFile();
    }

    private function addShops($order)
    {
        $line = "%s|%s|%s";
        $arrData = array();
        foreach ($order as $val) {
            $arrData[] = sprintf($line, '', $val->shop_name, $val->shop_domain);
        }
        $arrData = array_unique($arrData, SORT_STRING);
        foreach ($arrData as $val) {
            $this->WriteToFile->writeToCSVFile((explode('|', $val)));
        }
        $this->loadDateInfile("shops");

    }

    private function addUsers($order)
    {
        $line = "%s|%s|%s|%s";
        $arrData = array();
        foreach ($order as $val) {
            $arrData[] = sprintf($line, '', $val->user_first_name, $val->user_last_name, $val->user_email);
        }
        $arrData = array_unique($arrData, SORT_STRING);
        foreach ($arrData as $val) {
            $this->WriteToFile->writeToCSVFile((explode('|', $val)));
        }
        $this->loadDateInfile("users");
    }

    private function getArrProducts($order)
    {
        $arrProducts = array();
        foreach ($order as $products) {
            foreach ($products->products as $value) {
                $arrProducts[] = $value->name;
            }
        }
        return $arrProducts;
    }

    private function addProducts($order)
    {
        $line = "%s|%s";
        $arrData = array();
        foreach ($this->getArrProducts($order) as $value) {
            $arrData[] = sprintf($line, '', $value);
        }
        $arrData = array_unique($arrData, SORT_STRING);
        foreach ($arrData as $val) {
            $this->WriteToFile->writeToCSVFile(explode('|', str_replace(array("\"", "'"), array("\"", "\'"), $val)));
        }
        $this->loadDateInfile("productsCatalog");
    }

    private function getArrCategories($order)
    {
        $arrCategories = array();
        foreach ($order as $products) {
            foreach ($products->products as $value) {
                $arrCategories[] = $value->product_categories;
            }
        }
        return $arrCategories;
    }

    private function addCategories($order)
    {
        $line = "%s|%s";
        $arrData = array();
        foreach ($this->getArrCategories($order) as $value) {
            foreach (explode(',', $value) as $category)
                $arrData[] = sprintf($line, '', trim($category));
        }
        $arrData = array_unique($arrData, SORT_STRING);
        foreach ($arrData as $val) {
            $this->WriteToFile->writeToCSVFile(explode('|', $val));
        }
        $this->loadDateInfile("categoriesCatalog");
    }

    private function addProduct_category($order)
    {
        $arrProducts = $this->getArrProducts($order);
        $arrIdProducts = $this->AccessToDB->getArrIdTableInDB("SELECT id, name FROM productsCatalog", 'name');
        $arrCategories = $this->getArrCategories($order);
        $arrIdCategories = $this->AccessToDB->getArrIdTableInDB("SELECT id, name FROM categoriesCatalog", 'name');
        $line = "%s|%s|%s";
        $arrData = array();
        for ($i = 0; $i < count($arrProducts); $i++) {
            foreach (explode(',', $arrCategories[$i]) as $category) {
                $arrData[] = sprintf($line, '', $arrIdProducts[$arrProducts[$i]], $arrIdCategories[trim($category)]);
            }
        }
        $arrData = array_unique($arrData, SORT_STRING);
        foreach ($arrData as $val) {
            $this->WriteToFile->writeToCSVFile(explode('|', $val));
        }
        $this->loadDateInfile("product_category");
    }

    private function addOrder_products($order)
    {
        $arrIdProducts = $this->AccessToDB->getArrIdTableInDB("SELECT id, name FROM productsCatalog", 'name');
        $line = "%s|%s|%s";
        $arrData = array();
        foreach ($order as $products) {
            $this->id_Order++;
            foreach ($products->products as $value) {
                $arrData[] = sprintf($line, '', $this->id_Order, $arrIdProducts[$value->name]);
            }
        }
        $arrData = array_unique($arrData, SORT_STRING);
        foreach ($arrData as $val) {
            $this->WriteToFile->writeToCSVFile(explode('|', $val));
        }
        $this->loadDateInfile("order_products");
        $this->id_Order -= $this->count;
    }

    private function addOrders($order)
    {
        $arrIdUsers = $this->AccessToDB->getArrIdTableInDB("SELECT id, user_email FROM users", 'user_email');
        $arrIdShops = $this->AccessToDB->getArrIdTableInDB("SELECT id, shop_domain FROM shops", 'shop_domain');
        $line = "%s|%s|%s|%s|%s|%s";
        $arrData = array();
        foreach ($order as $value) {
            $this->id_Order++;
            $arrData[] = sprintf($line, '', $arrIdShops[$value->shop_domain], $arrIdUsers[$value->user_email], $value->sum,
                $value->date, $this->id_Order);
        }
        $arrData = array_unique($arrData, SORT_STRING);
        foreach ($arrData as $val) {
            $this->WriteToFile->writeToCSVFile(explode('|', $val));
        }
        $this->loadDateInfile("orders");
    }
}

?>