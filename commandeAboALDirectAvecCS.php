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


$leCsClient0->refCs = 1011;
$leCsClient0->libelle = "INFOCSCLIENT0";

$leCsClient1->refCs = 1006;
$leCsClient1->libelle = "123456";

$leCsAbonnement->refCs = 1008;
$leCsAbonnement->libelle = "UNCSABO";

$leCsFacture->refCs = 1009;
$leCsFacture->libelle = "UNSCFACTURE";

$leCsLigneFacture->refCs = 1010;
$leCsLigneFacture->libelle = "UNCSLIGNEFACTURE";

// Création du tampon client
$leclient->codeClient = 0; //codeClient retourné par createOrUpdateCLientEx/ ou identification pour affecter la commande au client existant*
$leclient->nom = "LE NOM"; //optionnel - informatif
$leclient->prenom = "LE PRENOM"; //optionnel - informatif
$leclient->email = "unemail@tbsblue.com"; //optionnel - informatif
$leclient->codeIsoPays = "FR"; //optionnel - informatif
$leclient->portable = "0123456789"; //optionnel - informatif
$leclient->codeClientTransco = "1234HG"; //optionnel - informatif
$leclient->nePasModifierClient = 0; //permet de ne pas écraser les données client lors de la validation de la commande
//ajout des code de sélection sur la strucutre tamponClient
$codesSelection = array();
$codesSelection[0]= $leCsClient0; //ajout d'un CS sur client
$codesSelection[1]= $leCsClient1; //ajout d'un autre CS sur client
$codesSelection[2]= $leCsFacture; //ajout d'un CS sur facture
$leclient->codesSelection = $codesSelection;

//Création d'une ligne de commande d'abonnement
$ligneCommande0->codeTarif = "F4-48"; // codeTarif de l'abonnement
$ligneCommande0->refTarif = 6; // référence unique du tarif d'abonnement /* obligatoire */
$ligneCommande0->quantite = 1;  
$ligneCommande0->modePaiement = 2; //1 chèque - 2 CB - 3 RIB - 4 Virement
$ligneCommande0->montantTtc = 48; //le montant n'a pas d'importance car il ne peut pas etre forcé dans le cadre d'un abonnement
$ligneCommande0->typeAdresseLiv=0; //pour ne pas gérer d'adresse de livraison (l'adresse de livraison est gérée via la nouvelle API createOrUpdateAdresse)
//ajout des CS sur la ligne de commande
$codesSelection = array();
$codesSelection[0]= $leCsAbonnement; //CS sur l'abonnement
$ligneCommande0->codesSelection = $codesSelection;


//Création d'une ligne de commande d'article libre
$ligneCommande1->codeTarif = "MAG10"; // codeTarif de l'article libre
$ligneCommande1->refTarif = 51; // référence unique de l'article libre /* obligatoire */
$ligneCommande1->quantite = 1;  
$ligneCommande1->modePaiement = 2; //1 chèque - 2 CB - 3 RIB - 4 Virement
$ligneCommande1->montantTtc = 12; //le montant peut ête forcé, attention donc à passer le montant exact, sinon ne pas passer cette rubrique
$ligneCommande1->typeAdresseLiv=0; //pour ne pas gérer d'adresse de livraison (l'adresse de livraison est gérée via la nouvelle API createOrUpdateAdresse)
//ajout des CS sur la ligne de commande
$codesSelection = array();
$codesSelection[0]= $leCsLigneFacture; //CS sur la ligne de facture
$ligneCommande1->codesSelection = $codesSelection;

$ligneCommande = array();
$ligneCommande[0] = $ligneCommande0; 
$ligneCommande[1] = $ligneCommande1; 

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
