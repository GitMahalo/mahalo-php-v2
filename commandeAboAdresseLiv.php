<?php 

$clientSoap = new SoapClient(   
        "http://preprod.aboweb.com/aboweb/abmWeb?wsdl" ,array("trace" => 1, "exceptions" => 0)   
);

$username = "admin.webservices@inisante-test.com"; // votre login
$password = base64_encode(sha1("INS7874",TRUE)); //votre mdp

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
$leclient->codeClient = 543283; //codeClient retourné par createOrUpdateCLientEx pour affecter la commande au client existant
$leclient->nePasModifierClient = 1; //permet de ne pas écraser les données adresse client

//Création d'une ligne de commande d'abonnement prépayé (tarif d'abonnement gratuit dans Aboweb)
$ligneCommande0->codeTarif = "ML-I11-NO-CP2-175V"; // codeTarif de l'abonnement gratuit correspondant à 5euro de carte cadeau
$ligneCommande0->quantite = 1;  
$ligneCommande0->modePaiement = 2; //2 CB 
$ligneCommande0->typeAdresseLiv=5; 
$ligneCommande0->civiliteLiv = 'M';
$ligneCommande0->nomLiv = 'LABAS';
$ligneCommande0->prenomLiv = 'LOIN';
$ligneCommande0->adresse2Liv = '22 AV DE LA GARE';
$ligneCommande0->cpLiv = '1000000';
$ligneCommande0->villeLiv = 'PEKIN';
$ligneCommande0->codeIsoPaysLiv = "CN";

$ligneCommande = array();
$ligneCommande[0] = $ligneCommande0; 

$param = array();
$params->refEditeur = 670; //votre ref éditeur
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
