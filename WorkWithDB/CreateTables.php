<?php
class CreateTables{
    private $pdo;
    private $fileSQL;

    function __construct($fileSQL, PDO $pdo){
        $this->pdo = $pdo;
        $this->fileSQL = $fileSQL;
    }

    public function createTables(){
        $this->dropTables();
        $sql = file_get_contents($this->fileSQL);
        try{
            $this->pdo->exec($sql);
        }catch (PDOException $e){
            echo $sql . "<br>" . $e->getMessage();
        }
    }
    private function dropTables(){
        $sql = "DROP TABLE IF EXISTS orders, order_products, product_category, users, shops, productsCatalog, categoriesCatalog";
        try{
            $this->pdo->exec($sql);
        }catch (PDOException $e){
            echo $sql . "<br>" . $e->getMessage();
        }
    }
}
?>