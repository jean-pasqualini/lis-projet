<?php
/* les diff�rents type de rendu sont don�e par ordre de priorit� */
$_CONFIGURATION['MODULE']['view']=array("canvas",        // Rendu canvas HTML5
                                        "lisrender");    // Rendu sur serveur

$_CONFIGURATION['MODULE']['audio']=array("audioapi",     // Rendu avec audioapi html5
                                         "jsmidi");      // Rendu sur bibliotheque js midi inconu (tous navigateurs)
?>