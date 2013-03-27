<?php
/**
 *
 * @author flavio.sena
 * @version 0.1
 *
 * date: 2012-12-20
 *
 */
class SuperFTP {
    private $server = 'localhost';
    private $port = '21';
    private $timeout = '90';
    private $user;
    private $password;
    private $conn = FALSE;
    private $mode = FTP_BINARY;
    private $error = array();

    function __construct() {
    }
    private function getConn(){
        if($this->conn === FALSE) {
            $this->connect();
        }
        return $this->conn;
    }
    public function connect() {
        $return = FALSE;
        $this->conn = @ftp_connect($this->server, $this->port, $this->timeout);

        if($this->conn !== FALSE) {
            $return = @ftp_login($this->conn, $this->user, $this->password);
            if($return === FALSE) {
                array_push($this->error, 'Login incorrect');
                $this->conn = FALSE;
            }
        } else {
            array_push($this->error, 'Server not found');
        }
        return $return;
    }
    public function getError(){
        return $this->error;
    }
    public function setServer($param){
        $this->server = $param;
    }
    public function setPort($param){
        if(is_integer($param) === FALSE) {      
            trigger_error ( '$param must be a integer. Received ' . var_export($param, TRUE) , 256 );
        }
        $this->port = $param;
    }
    public function setTimeout($param){
        if(is_integer($param) === FALSE) {      
            trigger_error ( '$param must be a integer. Received ' . var_export($param, TRUE) , 256 );
        }
        $this->timeout = $param;
    }
    public function setUser($param){
        $this->user = $param;
    }
    public function setPassword($param){
        $this->password = $param;
    }
    public function setMode($param){
        $this->mode = $param;
    }
    public function ls($paran = '.'){
        $return = @ftp_nlist($this->getConn(), $paran);
        if($return === NULL) {
            array_push($this->error, 'Couldn\'t list directories');   
            $return = array();
        }
        return $return;
    }
    public function getList($paran = '.'){
        return $this->ls($paran);
    }
    public function put($localFile, $remoteFile = NULL){
        if(file_exists($localFile)) {        
            if($remoteFile === NULL) {
                $remoteFile = $localFile;
            }
            if(@ftp_put($this->getConn(), $remoteFile, $localFile, $this->mode) === FALSE) {
                array_push($this->error, 'There was a problem while uploading ' . $localFile . ' to ' . $remoteFile);
            }            
        } else {
            array_push($this->error, 'File not found ' . $localFile);
        }
    }
    public function pwd() {
        $return = @ftp_pwd($this->getConn());
        if($return === NULL) {
            array_push($this->error, 'Couldn\'t list directories');            
        }
        return $return;
    }
    public function chdir($param) {
        $return = @ftp_chdir($this->getConn(), $param);
        if($return === FALSE) {
            array_push($this->error, 'Can\'t change directory to ' . $param);
        }
        return $return;
    }
    function __destruct() {
        @ftp_close($this->conn);
    }
}
/*
$superFTP = new SuperFTP();
$superFTP->setServer('192.168.1.223');
$superFTP->setPort(21);
$superFTP->setTimeout(10);
$superFTP->setUser('senartes');
$superFTP->setPassword('123456');
//$superFTP->connect();
//print_r($superFTP->getList('./public_html/ci'));
$superFTP->chdir('./public_html/ci');
print_r($superFTP->pwd());
//$superFTP->put('file.txt');
print_r($superFTP->getError());
*/