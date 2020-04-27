<?php 

///////// COMMANDE SIMPLE PAIEMENT STRIPE ABONNEMENT ADL /////

/*Après retour STRIPE pour création CB - envoyer le TOKEN à ABOWEB pour création CB dans Aboweb*/
/*le premier prélèvement ne doit pas être éxécuté par STRIPE mais par Aboweb. Stripe ne doit faiure qu'un empreinte de la CB */


/* --createCarteBancaire-- */
$clientSoap = new SoapClient(   
        "http://www.aboweb.com/aboweb/CarteBancaireService?wsdl" ,array("trace" => 1, "exceptions" => 0)   
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

$laCb->prestataire = 2; //STRIPE
$laCb->cbCode = "STR02";
$laCb->token = "TEST_TOKEN01";//TOKEN retourné par Stripe lors de la capture de CB
$laCb->dateVal = "1910";
$laCb->lastNumbers = "1664";

$param->createCarteBancaire = $laCb;
$response = $clientSoap->createCarteBancaire($param);

print "<br>REPONSE<br>";
print_r($response);   
print "<br>FIN REPONSE<br>"; 
  
print "<br><br><pre>\n";   
print "Request :\n".htmlspecialchars($clientSoap->__getLastRequest()) ."\n";   
print "Response:\n".htmlspecialchars($clientSoap->__getLastResponse())."\n";   
print "</pre>";  


/* Envoi de la commande en passant la reférence unique de la CB retournée par le WS ci-dessus 
   + mode de paiement = 6 dans les lignes de commandes */
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
$leclient->codeClient = 000; //codeClient retourné par createOrUpdateCLientEx pour affecter la commande au client existant
$leclient->nePasModifierClient = 1; //permet de ne pas écraser les données adresse client
$leclient->noCommandeBoutique = "HYIUN45IJ"; //on peut passer ici une référence personnalisée de commande
$leclient->refCarteBancaire = "123"; //ref unique de la CB retournée par le WS createCarteBancaire

//Création d'une ligne de commande d'abonnement
$ligneCommande0->codeTarif = "F4-38"; // codeTarif de l'abonnement ADL
$ligneCommande0->quantite = 1;  
$ligneCommande0->modePaiement = 6; //6 pour prélèvement sur CB. La commande sera intégrée avec génération d'une facture non soldée et X prélèvements en fonction des paramétrages du tarif
$ligneCommande0->montantTtc = 3; //le montant n'a pas d'importance car il ne peut pas etre forcé dans le cadre d'un abonnement
$ligneCommande0->typeAdresseLiv=0; //pour ne pas gérer d'adresse de livraison (l'adresse de livraison est gérée via la nouvelle API createOrUpdateAdresse)

$ligneCommande = array();
$ligneCommande[0] = $ligneCommande0; 

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
