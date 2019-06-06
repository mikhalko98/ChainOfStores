<?php
require_once "./WorkWithDB/DbConnectManager.php";
require_once "./WorkWithDB/CreateTables.php";
require_once "./WorkWithDB/FillDB.php";
require_once "./config.php";

class CreateAndFill
{
    function __construct(){}

    function run()
    {
        $Config = new Config();
        $ConnectToDBChainOfStores = new DbConnectManager($Config->getDns(), $Config->getUser(), $Config->getPass());
        $DB_ChainOfStores = $ConnectToDBChainOfStores->getdbh();

        $CreateTables = new CreateTables("./sql/schema.sql", $DB_ChainOfStores);
        $CreateTables->createTables();

        $FillDBChainOfStores = new FillDB($DB_ChainOfStores);
        $FillDBChainOfStores->readAndFill("purchase_log.json");

        $DB_ChainOfStores = null;
    }
}
?>