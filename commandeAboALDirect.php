<?php  
 
$clientSoap = new SoapClient(   
        "http://www.aboweb.com/aboweb/abmWeb?wsdl" ,array("trace" => 1, "exceptions" => 0)   
);

$username = "admin.webservices@ws-sandbox.fr"; // votre login
$password = base64_encode(sha1("WS1234",TRUE)); //votre mdp

$securityHeader ='<wsse:Security xmlns:wsse="http://docs.oasis-open.org/wss/2004/01/oasis-200401-wss-wssecurity-secext-1.0.xsd">
					<wsse:UsernameToken>
						<wsse:Username>'.$username.'</wsse:Username>
						<wsse:Password>'.$password.'</wsse:Password>
					</wsse:UsernameToken>
				</wsse:Security>';
 
$authvalues = new SoapVar($securityHeader,XSD_ANYXML);
$header = new SoapHeader("http://www.gesmag.com/","Security",$authvalues);
$clientSoap->__setSoapHeaders($header);

// Création du tampon client
$leclient->codeClient = "100034"; //codeClient retourné par createOrUpdateCLientEx
$leclient->nom = "LE NOM"; //optionnel - informatif
$leclient->prenom = "LE PRENOM"; //optionnel - informatif
$leclient->email = "unemail@tbsblue.com"; //optionnel - informatif
$leclient->portable = "0123456789"; //optionnel - informatif
$leclient->noCommandeBoutique = "JHHU56UY"; //optionnel - votre numéro de commande boutique
$leclient->nePasModifierClient = 1; //permet de ne pas écraser les données client lors de la validation de la commande

//Création d'une ligne de commande d'abonnement
$ligneCommande0->codeTarif = "F4-48"; // codeTarif de l'abonnement
$ligneCommande0->refTarif = 6; // référence unique du tarif d'abonnement /* obligatoire */
$ligneCommande0->quantite = 1;  
$ligneCommande0->modePaiement = 2; //1 chèque - 2 CB - 3 RIB - 4 Virement
$ligneCommande0->montantTtc = 48; //le montant n'a pas d'importance car il ne peut pas etre forcé dans le cadre d'un abonnement
$ligneCommande0->typeAdresseLiv=0; //pour ne pas gérer d'adresse de livraison (l'adresse de livraison est gérée via la nouvelle API createOrUpdateAdresse)

//Création d'une ligne de commande d'article libre
$ligneCommande1->codeTarif = "MAG10"; // codeTarif de l'article libre
$ligneCommande1->refTarif = 51; // référence unique de l'article libre /* obligatoire */
$ligneCommande1->quantite = 1;  
$ligneCommande1->modePaiement = 2; //1 chèque - 2 CB - 3 RIB - 4 Virement
$ligneCommande1->tauxRemise = 10; //taux de remise toujours en pourcentage /*optionnel*/
$ligneCommande1->montantTtc = 12; //le montant peut ête forcé, attention donc à passer le montant exact, sinon ne pas passer cette rubrique
$ligneCommande1->typeAdresseLiv=0; //pour ne pas gérer d'adresse de livraison (l'adresse de livraison est gérée via la nouvelle API createOrUpdateAdresse)

//Création d'une ligne de commande d'article libre
$ligneCommande2->codeTarif = "MAG09"; // codeTarif de l'article libre
$ligneCommande2->refTarif = 5; // référence unique de l'article libre /* obligatoire */
$ligneCommande2->quantite = 1;  
$ligneCommande2->modePaiement = 2; //1 chèque - 2 CB - 3 RIB - 4 Virement
$ligneCommande2->montantTtc = 12; //le montant peut ête forcé, attention donc à passer le montant exact, sinon ne pas passer cette rubrique
$ligneCommande2->typeAdresseLiv=0; //pour ne pas gérer d'adresse de livraison (l'adresse de livraison est gérée via la nouvelle API createOrUpdateAdresse)

$ligneCommande = array();
$ligneCommande[0] = $ligneCommande0; 
$ligneCommande[1] = $ligneCommande1; 
$ligneCommande[2] = $ligneCommande2; 

$param = array();
$params->refEditeur = 317; //votre ref éditeur
$params->refSociete = 1; //votre ref société
$params->clientTampon = $leclient;
$params->lstLignePanierTampon = $ligneCommande;

//envoi de la commande à Aboweb
$response = $clientSoap->ABM_CREATION_FICHIER_ABM($params);

print "<br>REPONSE<br>";
print_r($response);   
print "<br>FIN REPONSE<br>"; 
  
print "<br><br><pre>\n";   
print "Request :\n".htmlspecialchars($clientSoap->__getLastRequest()) ."\n";   
print "Response:\n".htmlspecialchars($clientSoap->__getLastResponse())."\n";   
print "</pre>";  

$resultat_commande = $response->return;
print "<br><br>Resultat de la commande = ".$resultat_commande->refAction."<br>"; 

?>
