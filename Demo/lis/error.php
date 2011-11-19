<?php
/**
 * Gestionaire d'erreur
 * @author internet
 * @license GPL
 * @version InDev
 * @package FrameworkLis
*/

set_exception_handler(array("LisException","handleException"));
set_error_handler("errorToException");
register_shutdown_function("FatalerrorToException");

function FatalerrorToException()
{
    $error = error_get_last();
    if($error != null)
    {
        chdir($_SERVER["DOCUMENT_ROOT"].ZF\GetBase()."/");
        
        throw new ZeroException($error['message'],$error["file"],$error["line"]);
        exit();
    }
}

function errorToException($code, $msg, $file, $line, $context)
{
    throw new LisException($msg, $file, $line);
    exit();
}

class LisException extends Exception
{
    private $ZeroFramework;

    private $trace;
    
    public function __construct($message, $file = "", $line = "", $trace = "")
    {
        if(!empty($file)) { $this->SetFile($file); }
        if(!empty($line)) { $this->SetLine($line); }
        if(!empty($trace)) { $this->SetTrace($trace); }
        parent::__construct($message,0);
        $this->showError();
        exit();
    }
    
    public function SetFile($value)
    {
        $this->file = $value;
    }
    
    public function SetLine($value)
    {
        $this->line = $value;
    }
    
    public function SetMessage($value)
    {
        $this->message = $value;
    }
    
    public function SetTrace($value)
    {
        $this->trace = $value;
    }
    
    public function GetPartieCode()
    {
        $retour = "";
        
        $file = fopen($this->getFile(), "r");
        
        if($file)
        {
            $i = 1;
            while(!feof($file))
            {
                $buffer = fgets($file);
                if($i==$this->getLine()) $retour = $buffer;
                $i++;
            }
            
            fclose($file);
        }
        
        return $retour;
    }
    
    public function showError()
    {
 /*
          $information = array(
                                "fichier" => $this->getFile(),
                                "line" => $this->getLine(),
                                "message" => $this->getMessage(),
                                "traces" => $this->getTrace(),
                                "partie_code" => $this->GetPartieCode()
                            );
	*/

	echo "Erreur | ".date("d/m/y h:i")." : ".$this->getFile()." (".$this->getLine().") : \r\n";
	echo $this->getMessage();
	echo "\r\n\r\n";
	
    }
    
    public static function handleException(Exception $e)
    {
        throw new LisException($e->getMessage(), $e->getFile(), $e->getLine(), $e->getTrace());
        exit();
    }
}

?>
