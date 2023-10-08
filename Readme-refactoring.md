premier commit 

*j'ai remplcer DestinationRepository::getInstance()->getById($quote->destinationId);dans la ligne 31 par $destinationOfQuote


*j'ai supprimer la ligne redondante
"(strpos($text, '[quote:destination_name]') !== false) and $text = str_replace('[quote:destination_name]',$destinationOfQuote->countryName,$text);"

La ligne de code precedent recherche la presence de [quote:destination_name] dans le texte ($text) et la remplace par $destinationOfQuote->countryName, cette operation est dejq effectue dans la fonction replaceQuotePlaceholders

*j'ai changer la ligne "(strpos($text, '[user:first_name]') !== false) and $text = str_replace('[user:first_name]'       , ucfirst(mb_strtolower($_user->firstname)), $text);"
car : cette verification n est  pas strictement necessaire car la fonction str_replace elle meme ne produit aucun effet si la chaine de charactere a remplacer n'est pas presente dans le texte

deuxiem commit
j'ai extrait dans la foction ComputeText les fonctions replaceQuotePlaceholders, replaceDestinationLink , la fonction compute text est trop long et imbique plusieus fonction a l'interrieur , j'ai decider d'extraire ces deux fonction pour avoir un code 
plus claire et pour cibler facilement la fonction qui presente une erreur.

troisieme commit 
J'ai ajouter un commentaire sur les deux fonction que j'ai extrait dans computeText les commentaire sont exxentiel pour que les autres puisse comprendre facilement le code