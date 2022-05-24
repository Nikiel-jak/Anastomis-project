<?PHP
class Orion_DataBase extends Zend_Controller_Plugin_Abstract{

    protected $_db;
    protected $_config;
    protected $_env;
    protected $_file_path;
    protected $_dbName;

    public function __construct()
    {
        $this->_db = Zend_Registry::get('db');
        $this->_config = Zend_Registry::get('config');
        $this->setDbName(APPLICATION_ENV);
        $this->setFilePath(PUBLIC_PATH.'/backup/');
        $this->setFileName('actual.sql');   
    }
    
    protected function setFilePath($path)
    {
        $this->_file_path = $path;
    }
    
    protected function setFileName($name)
    {
        $this->_file_name = $name;
    }
    
    
    protected function setDbName($env)
    {
        $this->_dbName = $this->_config->$env->resources->db->params->dbname;
    }
    public function doBackUp()
    {
        $sqlQuery = "SHOW tables FROM " . $this->_dbName;
        $sqlResult = $this->_db->query($sqlQuery);
        
       // $sqlData = "-- System Backup -- SET SQL_MODE=\"NO_AUTO_VALUE_ON_ZERO\";
        //            -- Baza: `$this->_dbName`
        //            -- System Backup --\n\n";
        $sqlData ='';
        foreach($sqlResult as $queryTable)
        {
            $name  = 'Tables_in_'.$this->_dbName;
            $sqlTable = $queryTable->$name;
            $sqlQuery = "SHOW CREATE TABLE $sqlTable";
            $sqlResultB = $this->_db->query($sqlQuery);
            $queryTableInfo = $sqlResultB->fetch(PDO::FETCH_ASSOC);
            //$sqlData .= "\n\n---- Struktura dla tabeli `$sqlTable`--\n\n";
            $queryTableInfo['Create Table'] = str_replace('CREATE TABLE','CREATE TABLE IF NOT EXISTS',$queryTableInfo['Create Table']);
            $sqlData .= $queryTableInfo['Create Table'] . ";\n";
            //$sqlData .= "\n\n---- Wartosci tabeli `$sqlTable`--\n\n";
            $sqlQuery = "SELECT * FROM $sqlTable\n";
            $sqlResultC = $this->_db->query($sqlQuery);
            foreach ($sqlResultC as $queryRecord) 
            {
                $sqlData .= "INSERT INTO $sqlTable VALUES (";
                $sqlRecord = "";
                foreach( $queryRecord as $sqlField => $sqlValue ) 
                {
                    $sqlRecord .= "'$sqlValue',";
                }
                $sqlData .= substr( $sqlRecord, 0, -1 );
                $sqlData .= ");\n";
            }
        }
        return $sqlData;    
    }
    public function saveLocal($data)
    {
        $file_name = 'backup_'.$this->_dbName.'_'.date('H_i_d_m_y').'.sql';
        $file = $this->_file_path . $file_name;
        if(is_writable($this->_file_path)) {
        	file_put_contents($file, $data);
            return true;
        }
    }
    
    public function exportDb($data)
    {
        $user = "u61769415";
        $pass = "oko1234";
        $host = "www.e-orion.pl";
        $file = "actual.sql";
        
        $tmp_path = PUBLIC_PATH.'/tmp/';
        $tmp_file = $tmp_path . $this->_file_name;
        if(is_writable($tmp_path)) {
        	file_put_contents($tmp_file, $data);
        }
    
        $ftp_path = '/public/backup/actual.sql';
        
        $conn_id = ftp_connect($host, 21) or die ("Cannot connect to host");     
        ftp_login($conn_id, $user, $pass) or die("Cannot login"); 
        ftp_chdir($conn_id, '/public/backup');
        $upload = ftp_put($conn_id, $ftp_path, $tmp_file, FTP_ASCII);
        if($upload){ 
            $ftpsucc=1; 
        } else { 
            $ftpsucc=0; 
        }
        ftp_close($conn_id);
        unlink($tmp_file);
        if($upload){
            return true;
        }
        return false;    
    }
    
    public function importDb()
    {
        $place = 'http://e-orion.pl/backup/actual.sql';
        $fp = fopen($place, 'r');
        $data = (stream_get_contents($fp));
        $results = $this->_db->query($data);
        if($results->errorCode() === '00000'){
            return true;
        }
        return false;
       
    }

}
