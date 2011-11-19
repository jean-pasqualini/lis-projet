<?php
/**
 * Fichier de configuration de LIS
 * @author Jean pasqualini <jpasqualini75@gmail.com>
 * @license GPL
 * @version InDev
 * @package ConfigurationLis
*/

// les différents type de rendu sont donnée par ordre de priorité

$_CONFIGURATION['MODULE']['view']=array("canvas",        // Rendu canvas HTML5
                                        "lisrender");    // Rendu sur serveur (Obsoléte)

$_CONFIGURATION['MODULE']['audio']=array("audioapi",     // Rendu avec audioapi html5
                                         "jsmidi");      // Rendu sur bibliotheque js midi inconu (tous navigateurs)
?>