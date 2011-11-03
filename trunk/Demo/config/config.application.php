<?php
/*
 Fichier de configuration de LIS
 @author Jean pasqualini <jpasqualini75@gmail.com>
 @license GPL
 @version InDev
*/

// les diff�rents type de rendu sont donn�e par ordre de priorit�

$_CONFIGURATION['MODULE']['view']=array("canvas",        // Rendu canvas HTML5
                                        "lisrender");    // Rendu sur serveur (Obsol�te)

$_CONFIGURATION['MODULE']['audio']=array("audioapi",     // Rendu avec audioapi html5
                                         "jsmidi");      // Rendu sur bibliotheque js midi inconu (tous navigateurs)
?>