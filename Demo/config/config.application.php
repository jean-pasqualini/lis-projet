<?php
/* les différents type de rendu sont donée par ordre de priorité */
$_CONFIGURATION['MODULE']['view']=array("canvas",        // Rendu canvas HTML5
                                        "lisrender");    // Rendu sur serveur

$_CONFIGURATION['MODULE']['audio']=array("audioapi",     // Rendu avec audioapi html5
                                         "jsmidi");      // Rendu sur bibliotheque js midi inconu (tous navigateurs)
?>