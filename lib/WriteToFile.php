<?php
class WriteToFile{

	private $nameCSV;
    private $nameSQL;

	function __construct($nameCSV, $nameSQL)
    {
        $this->nameCSV = $nameCSV;
        $this->nameSQL = $nameSQL;
    }
	public function writeToCSVFile($data){
	    $fw = fopen($this->nameCSV, 'a+');
	    fputcsv($fw,($data));
	    fclose($fw);
	}
    public function clearCSVFile(){
        $fw = fopen($this->nameCSV, 'w');
        fclose($fw);
    }
    public function writeToSQLFile($data){
        $fw = fopen($this->nameSQL, 'a+');
        fwrite($fw,($data));
        fclose($fw);
    }
    public function clearSQLFile(){
        $fw = fopen($this->nameSQL, 'w');
        fclose($fw);
    }

    /**
     * @return string
     */
    public function getNameCSV()
    {
        return $this->nameCSV;
    }

    /**
     * @return string
     */
    public function getNameSQL()
    {
        return $this->nameSQL;
    }
}
?>