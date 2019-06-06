<?php
class WriteCSVfile{

    private $name;

    function __construct($name){
        $this->name = $name;
    }

    public function writeToFile($data){
        $fw = fopen($this->name, 'a+');
        fputcsv($fw,explode(",", $data));
        fclose($fw);
    }

    /**
     * @return mixed
     */
    public function getName()
    {
        return $this->name;
    }

}
?>