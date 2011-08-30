<?php
Class Object2D extends Object {
    
    public function __construct($register=true,$parent = null,$name = "")
    {
        if($register)
        {
            echo "[INFO] Objet (".get_class($this).") de type 2D initialiser\r\n";
        }
        if($parent != null)
        {
            $this->Position = "Relative";
        }
        //$this->handle_application=$handle_canvas->GetHandleApplication();
        parent::__construct($parent,$name);
    }
    
    public function SetBackgroundColor($color)
    {
        $this->Background=$color;
        //ApplicationLIS::GetModule("Canvas")->FillStyle($this->Background);
        return $this;
    }
    
    public function GetAbsolutePositionX()
    {
        if($this->parent!=null) return $this->parent->GetAbsolutePositionX() + $this->PositionX;
        return $this->PositionX;
    }
    
    public function GetAbsolutePositionY()
    {
        if($this->parent!=null) return $this->parent->GetAbsolutePositionY() + $this->PositionY;
        return $this->PositionY;
    }
    
    // Deplacement de l'objet par position
    public function MoveTo($x,$y)
    {
        //On informe de la mise a jour de l'objet
        $this->SetUpdated(true);
        
        $this->PositionX=$x;
        $this->PositionY=$y;
        return $this;
    }
    
    // Deplacement de l'objet en relatif
    public function MoveOf($x,$y)
    {
        //On informe de la mise a jour de l'objet
        $this->SetUpdated(true);
        
        $this->PositionX+=$x;
        $this->PositionY+=$y;
        return $this;
    }
}
?>