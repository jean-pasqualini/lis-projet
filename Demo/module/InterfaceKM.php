<?php
/**
  * Module d'interaction utilisateur via périphérique 
  * Ce module est en cours de dévellopement et expérimental
  * @author Jean pasqualini <jpasqualini75@gmail.com>
  * @license GPL
  * @version InDev
  * @package ModuleLis
*/
class module_InterfaceKM extends ModuleBase implements IModuleBase {
    
	/**
	  * @access private
	  * @var int $positionX Position de la souris en X
	*/
	private $positionX=0;
	
	/**
	 * @access private
	 * @var int $positionY Position de la souris en Y
	*/
	private $positionY=0;
		
	/**
	 * @access private
	 * @var SProxy Proxy d'handler pour la position de la souris
	*/
	private $handle_move = null;
	
	/**
	 * @access private
	 * @var SProxy Proxy d'handler pour le click de souris
	*/
	private $handle_click = null;
	
	/**
	 * @access private
	 * @var SProxy Proxy d'handler pour le double click de souris
	*/
	private $handle_dblclick = null;
		
	/**
	 * @access private
	 * @var SProxy Proxy d'handler pour la pression et relachement d'une double
	*/	
	private $handle_keypress = null;
	
	/**
	 * @access private
	 * @var SProxy Proxy d'handler pour le relachement d'une touche
	*/
	private $handle_keyup = null;
	
	/**
	 * @access private
	 * @var SProxy Proxy d'handler pour le relachement d'une touche
	*/
	private $handle_keydown = null;
	
  /**
   * Recupére le nom du module
   * @access public
   * @return string Le nom du module
  */
  public function GetModuleName()
  {
    return "InterfaceKM";
  }

  /**
   * Récupere la description du module
   * @access public
   * @return string La description du module
  */
  public function GetModuleDescription()
  {
      return "Intercept keyboard and mouse";
  }
  
  /**
   * Récupere la version du module
   * @access public
   * @return string La version du module
  */
  public function GetVersion()
  {
      return "1.0";
  }
  
  /**
   * Récupere les dépendances module serveur
   * @access public
   * @return Array Les dépendances serveur
  */
  public function GetDependanceServer()
  {
    // Declare les modules serveur dont nous avons besoin
    return array();
  }
  
  /**
   * Récupere les dépendances module client
   * @access public
   * @return Array Les dépendances client
  */
  public function GetDependanceClient()
  {
    return array(); 
  }
        
	
    public function SetHandle_mouse($handle_move = null,$handle_click = null,$handle_dblclick = null)
    {    	   	
    	if($handle_move != null) $this->SetHandle_mouse_move($handle_move);
    	if($handle_click != null) $this->SetHandle_mouse_click($handle_click);
    	if($handle_dblclick != null) $this->SetHandle_mouse_dblclick($handle_dblclick);
    }
    
    public function SetHandle_keyboard($handle_keypress = null , $handle_keyup = null , $handle_keydown = null)
    {
    	if($handle_keypress != null) $this->SetHandle_keypress($handle_keypress);
    	if($handle_keyup != null) $this->SetHandle_keyup($handle_keyup);
    	if($handle_keydown != null) $this->SetHandle_keydown($handle_keydown);
    }
    
    public function SetHandle_keypress($handle_keypress)
    {
    	if($this->handle_keypress == null) $this->handle_keypress = new SProxy();
    	$this->handle_keypress->SProxy_Add($handle_keypress);
    }
    
    public function SetHandle_keyup($handle_keyup)
    {
    	if($this->handle_keyup == null) $this->handle_keyup = new SProxy();
    	$this->handle_keyup->SProxy_Add($handle_keyup);
    }
    
    public function SetHandle_keydown($handle_keydown)
    {
    	if($this->handle_keydown == null) $this->handle_keydown = new SProxy();
    	$this->handle_keydown->SProxy_Add($handle_keydown);
    }
    
    public function SetHandle_mouse_move($handle_move)
    {
    	if($this->handle_move == null) $this->handle_move = new SProxy();
        $this->handle_move->SProxy_Add($handle_move);
    }
    
    public function SetHandle_mouse_click($handle_click)
    {
    	if($this->handle_click == null) $this->handle_click = new SProxy();
        $this->handle_click->SProxy_Add($handle_click);
    }
    
    public function SetHandle_mouse_dblclick($handle_dblclick)
    {
    	if($this->handle_dblclick == null) $this->handle_dblclick = new SProxy();
        $this->handle_dblclick->SProxy_Add($handle_dblclick);
    }
    
    public function Recept()
    {
        if(!$this->recv()) { return false; } else { return true; }
    }
    
    public function GetPosition($retreive=false)
    {
        if(!$this->positionX && !$this->positionY || $retreive==true)
        {
            $data=array("Type" => "view" , "Action" => "GetPosition");
            $this->send($data);
            //socket_recv($this->socket,$buffer,2048,0);
            $data = socket_read($this->socket,2048,PHP_NORMAL_READ);
            //echo "retour : ".$data."\n";
            $data = json_decode($data,true);
            // On verifie que les donée recue sont bien du type objet
            if(is_array($data) && isset($data["X"]) && isset($data["Y"]))
            {
                $this->positionX=$data["X"];
                $this->positionY=$data["Y"];
                //echo "X: ".$data->X." , Y: ".$data->Y."\n";
            }
        }
        return array("X" => $this->positionX,"Y" => $this->positionY);
    }
    
    public function MouseMove($positionX,$positionY)
    {
        $this->positionX=$positionX;
        $this->positionY=$positionY;

        $this->handle_move->MouseMove($this->positionX,$this->positionY);
    }
    
    public function MouseClick()
    {
        $this->handle_click->MouseClick($this->positionX,$this->positionY);
    }
    
    public function MouseDblClick()
    {
        $this->handle_dblclick->MouseDblClick($this->positionX,$this->positionY);
    }
    
    public function KeyPress($ascii_code)
    {
    	$this->handle_keypress->KeyPress($ascii_code);        
    }
    
    public function KeyUp()
    {
        
    }
    
    public function KeyDown()
    {
        
    }
}
?>