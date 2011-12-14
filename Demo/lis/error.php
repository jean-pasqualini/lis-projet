<?php
/**
 * Gestionaire d'erreur
 * @author internet
 * @license GPL
 * @version InDev
 * @package FrameworkLis
*/

//set_exception_handler(array("LisException","handleException"));
//set_error_handler("errorToException");
//register_shutdown_function("FatalerrorToException");

function FatalerrorToException()
{
    $error = error_get_last();
    if($error != null)
    {
        chdir($_SERVER["DOCUMENT_ROOT"]."/");
        
        throw new LisException($error['message'],$error["file"],$error["line"]);
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
    private $Application;

    private $trace;
    
    public function __construct($message, $file = "", $line = "", $trace = "")
    {
        if(!empty($file)) { $this->SetFile($file); }
        if(!empty($line)) { $this->SetLine($line); }
        if(!empty($trace)) { $this->SetTrace($trace); }
		
		$this->Application = ApplicationLis::GetInstance();
		
        parent::__construct($message,0);
        $this->showError();
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
	
	$ÎnfoApplication = new ReflectionObject($this->Application);

	echo "=============================="."\r\n";
	echo get_class($this)." | ".date("d/m/y h:i")." : ".$this->getFile()." (".$this->getLine().") \r\n";
	echo "Message d'erreur : ".$this->getMessage()."\r\n";
	echo "Nom de l'application : ".$ÎnfoApplication->getName()."\r\n";
	echo "Fichier main de l'application : ".$ÎnfoApplication->getFileName()."\r\n";
	echo "Adresse d'écoute de l'applicaiton : ".$this->Application->getIp().":".$this->Application->getPort()."\r\n";
	echo "Modules chargées : ".implode(", ", ApplicationLIS::getNameModules())."\r\n\r\n";
	echo "Partie de code : "."\r\n";
	echo $this->GetPartieCode()."\r\n";
	echo "==============================";
	echo "\r\n\r\n";
	
    }
    
    public static function handleException(Exception $e)
    {
        throw new LisException($e->getMessage(), $e->getFile(), $e->getLine(), $e->getTrace());
        exit();
    }
	
	public function getDetailsComment($comment)
	{
		if($comment !== false)
		{
			$expr = "/((?:\/\*(?:[^*]|(?:\*+[^*\/]))*\*+\/)|(?:\/\/.*))/"; 
			
			echo $comment;
			
		}
		else {
			echo "Aucune information";
		}
	}
}

/**
 * Exception de parse des fichier css
 * @author jean pasqualini <jpasqualini75@gmail.com>
 * @version InDev
 * @license GPL
 */
class CssParseException extends LisException {
	
	/**
	 * Le constructeur de l'execption de parse des fichiers css
	 * @access public
	 * @param CssParseur $instance Instance du parseur css 
	 * @param string $message Message d'erreur
	 * @return CssParseException Instance de l'execoption de parse des fichier css
	 */
	public function __construct($instance, $message)
	{
		$this->SetFile($instance->getFile());
		$this->SetLine($instance->getLine());
		
		parent::__construct($message);
	}
}

/**
 * Exception de communication avec le client
 * @author jean pasqualini <jpasqualini75@gmail.com>
 * @version InDev
 * @license GPL
 */
class SocketException extends  LisException {
	
	/**
	 * Le constructeur de l'exeception de communication avec le licent
	 * @access public
	 * @param ApplicationLIS $instance Instance de l'application 
	 * @param string $message Message d'erreur
	 * @return SocketException Instance de l'exeception de comunication avec le client
	 */
	public function __construct($instance, $message)
	{
		parent::__construct($message);
	}
}

/**
 * Exception de module non chargé
 * @author jean pasqualini <jpasqualini75@gmail.com>
 * @version InDev
 * @license GPL
 */
class ModuleNotLoadedException extends LisException {
	
	private $instanceModule;
	
	/**
	 * Le constructeur de l'exeception de module non chargé
	 * @access public
	 * @param ApplicationLIS $instance Instance de l'application 
	 * @param string $message Message d'erreur
	 * @return SocketException Instance de l'exeception de module non chargé
	 */
	public function __construct($file, $instance, $message)
	{
		$this->instanceModule = $instance;
		if(file_exists("module/".$file.".php")) $this->setFile("module/".$file.".php");
		parent::__construct($message);
	}
}

?>
