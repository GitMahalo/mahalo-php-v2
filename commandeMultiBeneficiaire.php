<?php 
////////////////////////////////////////////////////////////////////
///////// COMMANDE MULTIBENEFICIAIRES TIERS PAYANT ONE SHOOT CB /////
////////////////////////////////////////////////////////////////////

$clientSoapClient = new SoapClient("http://www.aboweb.com/aboweb/ClientService?wsdl" ,array("trace" => 1, "exceptions" => 0));
$clientSoapCommande = new SoapClient("http://www.aboweb.com/aboweb/abmWeb?wsdl" ,array("trace" => 1, "exceptions" => 0));


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

$clientSoapClient->__setSoapHeaders($header);
$clientSoapCommande->__setSoapHeaders($header);


/* CREATION DU TIERS PAYANT */
$clientTP->email = 'lenouveautierspayant@email.fr';
$clientTP->typeClient = 1; //type de client (0 = normal, 1 = Tiers, 2 = Payé Par)
$clientTP->origineAbm = "ABM"; //Origine du client
$clientTP->civilite = 'M';
$clientTP->nom = 'LE TIERS';
$clientTP->prenom = 'LE TIERS';
$clientTP->societe = 'TP';
$clientTP->adresse1 = '';
$clientTP->adresse2 = '24 RUE DES FLEURS';
$clientTP->adresse3 = '';
$clientTP->cp = '92100';
$clientTP->ville = 'BOULOGNE BILLANCOURT';
$clientTP->motPasseAbm = 'LEMOTDEPASSE';
$clientTP->codeIsoPays = "FR";

$param->client = $clientTP;

$response = $clientSoapClient->createOrUpdateClientEx($param);
$codeClientTP = $response->codeClient;

print "<br><br><pre>\n";   
print "Request :\n".htmlspecialchars($clientSoapClient->__getLastRequest()) ."\n";   
print "Response:\n".htmlspecialchars($clientSoapClient->__getLastResponse())."\n";   
print "</pre>";  

print "<br>REPONSE - CODE TIERS<br>";   
print_r($response);   
print "<br>FIN REPONSE<br><br>"; 



/* CREATION DES BENEFICIARES */
//BENEFICIAIRE 1
$clientPP1->email = 'lenouveaupayepar1@email.fr';
$clientPP1->typeClient = 2; //type de client (0 = normal, 1 = Tiers, 2 = Payé Par)
$clientPP1->codeTiers = $codeClientTP; //code du tiers précédement créé ou identifié, obligatoire si type client = 2
$clientPP1->origineAbm = "ABM"; //Origine du client
$clientPP1->civilite = 'M';
$clientPP1->nom = 'LE PP1';
$clientPP1->prenom = 'LE PP1';
$clientPP1->societe = 'PP1';
$clientPP1->adresse1 = '';
$clientPP1->adresse2 = '1 RUE DES ARBRES';
$clientPP1->adresse3 = '';
$clientPP1->cp = '75017';
$clientPP1->ville = 'PARIS';
$clientPP1->motPasseAbm = 'LEMOTDEPASSEPP1';
$clientPP1->codeIsoPays = "FR";

$param->client = $clientPP1;

$response = $clientSoapClient->createOrUpdateClientEx($param);
$codeClientPP1 = $response->codeClient;

print "<br><br><pre>\n";   
print "Request :\n".htmlspecialchars($clientSoapClient->__getLastRequest()) ."\n";   
print "Response:\n".htmlspecialchars($clientSoapClient->__getLastResponse())."\n";   
print "</pre>";  

print "<br>REPONSE- PAYE PAR 1<br>";   
print_r($response);   
print "<br>FIN REPONSE<br><br>"; 

//BENEFICIAIRE 2
$clientPP2->email = 'lenouveaupayepar2@email.fr';
$clientPP2->typeClient = 2; //type de client (0 = normal, 1 = Tiers, 2 = Payé Par)
$clientPP2->codeTiers = $codeClientTP; //code du tiers précédement créé ou identifié, obligatoire si type client = 2
$clientPP2->origineAbm = "ABM"; //Origine du client
$clientPP2->civilite = 'M';
$clientPP2->nom = 'LE PP2';
$clientPP2->prenom = 'LE PP2';
$clientPP2->societe = 'PP2';
$clientPP2->adresse1 = '';
$clientPP2->adresse2 = '2 RUE DES ARBRES';
$clientPP2->adresse3 = '';
$clientPP2->cp = '75017';
$clientPP2->ville = 'PARIS';
$clientPP2->motPasseAbm = 'LEMOTDEPASSEPP2';
$clientPP2->codeIsoPays = "FR";

$param->client = $clientPP2;

$response = $clientSoapClient->createOrUpdateClientEx($param);
$codeClientPP2 = $response->codeClient;

print "<br><br><pre>\n";   
print "Request :\n".htmlspecialchars($clientSoapClient->__getLastRequest()) ."\n";   
print "Response:\n".htmlspecialchars($clientSoapClient->__getLastResponse())."\n";   
print "</pre>";  

print "<br>REPONSE - PAYE PAR 2<br>";   
print_r($response);   
print "<br>FIN REPONSE<br><br>"; 




/*CREATION DE LA COMMANDE */
// Création du tampon client			
$leclient->codeClient = $codeClientTP; //codeClient retourné par createOrUpdateCLientEx ou identifié pour affecter la commande au client existant
//passer éventuellement les infos clients - optionnel si nePasModifierClient = 1
$leclient->nom = 'LE TIERS';
$leclient->prenom = 'LE TIERS';
$leclient->email = 'lenouveautierspayant@email.fr';
$leclient->nePasModifierClient = 1; //si 0 il faut envoyer tous les éléments d'adresses, si 1 -> les éléments d'adresses sont optionnels
$leclient->noCommandeBoutique = "HYIUN45IJ"; //on peut passer ici une référence personnalisée de commande

//Création d'une ligne de commande pour chaque bénéficiaire
$ligneCommande = array();

$ligneCommande0->codeTarif = "F4-60"; // codeTarif
$ligneCommande0->quantite = 1;  
$ligneCommande0->modePaiement = 2; //2 CB pour le paiement STRIPE ONE SHOT -> a pour effet de générer facture soldée et règlement dans ABoweb lors de la validation
$ligneCommande0->montantTtc = 3; //le montant n'a pas d'importance car il ne peut pas etre forcé dans le cadre d'un abonnement
$ligneCommande0->typeAdresseLiv = 7; //la ligne de commande est affectée au payé Par identifié par le codeClientLiv
$ligneCommande0->codeClientLiv = $codeClientPP1;
$ligneCommande[0] = $ligneCommande0; 

$ligneCommande1->codeTarif = "F4-38"; // codeTarif
$ligneCommande1->quantite = 1;  
$ligneCommande1->modePaiement = 2; //2 CB pour le paiement STRIPE ONE SHOT -> a pour effet de générer facture soldée et règlement dans ABoweb lors de la validation
$ligneCommande1->montantTtc = 3; //le montant n'a pas d'importance car il ne peut pas etre forcé dans le cadre d'un abonnement
$ligneCommande1->typeAdresseLiv = 7; //la ligne de commande est affectée au payé Par identifié par le codeClientLiv
$ligneCommande1->codeClientLiv = $codeClientPP2;
$ligneCommande[1] = $ligneCommande1; 

$param = array();
$params->refEditeur = 317; //votre ref éditeur
$params->refSociete = 1; //votre ref société
$params->clientTampon = $leclient;
$params->lstLignePanierTampon = $ligneCommande;

//envoi de la commande à Aboweb
$response = $clientSoapCommande->ABM_CREATION_FICHIER_ABM($params);

print "<br>REPONSE<br>";
print_r($response);   
print "<br>FIN REPONSE<br>"; 
  
print "<br><br><pre>\n";   
print "Request :\n".htmlspecialchars($clientSoapCommande->__getLastRequest())."\n";   
print "Response:\n".htmlspecialchars($clientSoapCommande->__getLastResponse())."\n";   
print "</pre>";  

$resultat_commande = $response->return;
print "<br><br>Resultat de la commande = ".$resultat_commande->refAction."<br>"; 
