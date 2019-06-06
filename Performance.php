<?php
require_once "./WorkWithDB/DbConnectManager.php";
require_once "./WorkWithDB/CreateTables.php";
require_once "./WorkWithDB/FillDB.php";
require_once "./WorkWithDB/AccessToDB.php";
require_once "config.php";

class Performance
{
    private  $DB_ChainOfStores;

    function __construct(){}

    function conection(){
        $Config = new Config();
        $ConnectToDBChainOfStores = new DbConnectManager($Config->getDns(), $Config->getUser(), $Config->getPass());
        $this->DB_ChainOfStores = $ConnectToDBChainOfStores->getdbh();
    }
    function run()
    {
        $this->conection();
        $this->work();


        $this->DB_ChainOfStores = null;
    }
    function work(){
        $AccessToDB = new AccessToDB($this->DB_ChainOfStores);
        $ch = true;
        $this->menu();
        while($ch){
            echo "ENTER: ";
            $choice = trim(fgets(STDIN));
            switch ($choice){
                case '1':
                    $AccessToDB->firstSelect();
                    break;
                case '2':
                    $AccessToDB->secondSelect();
                    break;
                case '3':
                    $AccessToDB->thirdSelect();
                    break;
                case '4':
                    $AccessToDB->fourthSelect();
                    break;
                case '5':
                    $AccessToDB->fifthSelect();
                    break;
                case '6':
                    $AccessToDB->sixthSelect();
                    break;
                case 'add pur':
                    $AccessToDB->addPurchase();
                    break;
                case 'exit':
                    $ch = false;
                    break;
                case 'help':
                    $this->menu();
                    break;
                default:
                    echo "Incorrect \n";
                    break;
            }
        }
    }
    function menu(){
        echo "\t\t***MENU***\n";
        echo "add pur - add purchase\n";
        echo "exit - exit\n";
        echo "help - print menu\n";
        echo "1 - select all users (first/last name, email,count buy) who made a purchase of products from 'X' category in 'Y' shop for last N years\n";
        echo "2 - select names of all categories and count the number of purchases of products from that category\n";
        echo "3 - select users (first/last name, email,count buy, sum buy) who have more then N purchase in 'X' shop\n";
        echo "4 - show the amount of profit for the month for a particular store for the specified year\n";
        echo "5 - show all purchases made by the user by his email\n";
        echo "6 - select users (first/last name, email) who have purchases purchases in all shops\n";
    }
}