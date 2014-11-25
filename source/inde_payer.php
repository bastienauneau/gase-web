<?php
session_start();

	//require("inde_fonctionsACH.php");
	//require("fonctions_bd_gase.php");
	require("inde_fonctionsSTK.php");
	require("inde_fonctionsMC.php");
	require("inde_fonctionsAD.php");
	

	$soldeAdherent = SelectionSoldeAdherentMC($_SESSION['inde_adherent']);
	if (isset ($_POST['payer']))
	{
		//Vérifie si le montant de la commande est supérieur à 0.
		$totalTTC = $_SESSION['inde_montantPanier'];
		if($totalTTC > 0)
		{
		    //la maison fait crédit de 20Euro max !!
			if($totalTTC <= $soldeAdherent+20)
			{
				$nbRef = $_SESSION['inde_nbRefPanier'];
				
				$idAdherent = $_SESSION['inde_adherent'];
				
				DepenseMC($idAdherent,$totalTTC);
				$nouveauSolde = SelectionSoldeAdherentMC($idAdherent);
				
				
				$numeroAchat = EnregistrerAchatAdherent($idAdherent, $totalTTC, $nbRef);

				for ($compteur = 0; $compteur < $nbRef; $compteur++)
				{
					AchatSTK($numeroAchat, $_SESSION['inde_panier']['idRef'][$compteur], $_SESSION['inde_panier']['qteReference'][$compteur]);
				}
				
//				envoyerMail($idAdherent, $totalTTC);
$date = date("d-m-Y");
////ini_set("SMTP","smtp.bbox.fr");

$mail = stripslashes(SelectionMailAdherentAD($idAdherent)); // Déclaration de l'adresse de destination.
if (!preg_match("#^[a-z0-9._-]+@(hotmail|live|msn).[a-z]{2,4}$#", $mail)) // On filtre les serveurs qui rencontrent des bogues.
{
	$passage_ligne = "\r\n";
}
else
{
	$passage_ligne = "\n";
}
//=====Déclaration des messages au format texte et au format HTML.
/*
$message_txt = "Salut à tous, voici un e-mail envoyé par un script PHP.";
$message_html = "<html><head></head><body><b>Salut à tous</b>, voici un e-mail envoyé par un <i>script PHP</i>.</body></html>";
*/

/* Message texte */
$message_txt = "Vos achats du " . $date . "\n";

for($i = 0; $i < count($_SESSION['inde_panier']['nomReference']); $i++)
{
	$message_txt = $message_txt . stripslashes($_SESSION['inde_panier']['nomReference'][$i]) . "  [ " . round($_SESSION['inde_panier']['prixReference'][$i],2) . " euros ]\n"; 	 	
}

$message_txt = $message_txt . "\nTOTAL TTC : " . round($totalTTC,2) . " euros.\n"; 
$message_txt = $message_txt . "Le solde de votre compte MoneyCoop est maintenant de : " . round($nouveauSolde,2) . " euros.\n";
$message_txt = $message_txt . "Merci.";

/* Message html */
$message_html = "<html><head></head><body>";
$message_html = $message_html . "Vos achats du " . $date . "<br /><br />";

for($i = 0; $i < count($_SESSION['inde_panier']['nomReference']); $i++)
{
	$message_html = $message_html . $_SESSION['inde_panier']['qteReference'][$i] . " " . stripslashes($_SESSION['inde_panier']['nomReference'][$i]) . "  [ " . round($_SESSION['inde_panier']['prixReference'][$i],2) . " euros ]<br />"; 	
}

$message_html = $message_html . "<br />TOTAL TTC : " . round($totalTTC,2) . " euros.<br />"; 
$message_html = $message_html . "Le solde de votre compte MoneyCoop est maintenant de : " . round($nouveauSolde,2) . " euros.<br />";
$message_html = $message_html . "Merci.";

$message_html = $message_html . "</body></html>";


//==========
 
//=====Création de la boundary
$boundary = "-----=".md5(rand());
//==========
 
//=====Définition du sujet.
$sujet = "[Achats_au_GASE]Ticket achats";
//=========
 
//=====Création du header de l'e-mail.
$header = "From: <gasiersdelesclain@gmail.com>".$passage_ligne;
$header.= "Reply-to: <gasiersdelesclain@gmail.com>".$passage_ligne;
$header.= "MIME-Version: 1.0".$passage_ligne;
$header.= "Content-Type: multipart/alternative;".$passage_ligne." boundary=\"$boundary\"".$passage_ligne;
//==========
 
//=====Création du message.
$message = $passage_ligne."--".$boundary.$passage_ligne;
//=====Ajout du message au format texte.
$message.= "Content-Type: text/plain; charset=\"ISO-8859-1\"".$passage_ligne;
$message.= "Content-Transfer-Encoding: 8bit".$passage_ligne;
$message.= $passage_ligne.$message_txt.$passage_ligne;
//==========
$message.= $passage_ligne."--".$boundary.$passage_ligne;
//=====Ajout du message au format HTML
$message.= "Content-Type: text/html; charset=\"ISO-8859-1\"".$passage_ligne;
$message.= "Content-Transfer-Encoding: 8bit".$passage_ligne;
$message.= $passage_ligne.$message_html.$passage_ligne;
//==========
$message.= $passage_ligne."--".$boundary."--".$passage_ligne;
$message.= $passage_ligne."--".$boundary."--".$passage_ligne;
//==========
 
//=====Envoi de l'e-mail.
////mail($mail,$sujet,$message,$header);
error_log($message);
//==========
/*****************************************/

			
				echo "Achats " . $numeroAchat . " enregistree.<br />";
				echo "<div style=\"text-align:center\">Le solde de votre compte est maintenant de " . round($nouveauSolde,2) . " euros.</div>";
				echo "Merci.<br />";
							
				echo "
				    <br />
				    <li>Pour aller a la page d'accueil : <a href=\"index.php\">cliquez ici</a></li>
				";
			}
			else
			{
				echo "<div style=\"text-align:center; color: #FF0000\">Attention, le total de vos achats et superieur au solde de votre compte MoneyCoop.<br />Veuillez approvisionner votre MoneyCoop avant de re-enregistrer vos achats.</div>";  
				echo "
				    <br />
				    <li>Pour aller a la page d accueil : <a href=\"index.php\">cliquez ici</a>cliquez ici</a></li>
				";
			}
		}
		else
		{
			include '1listeRefCategorie.php'; 
			echo 'Le panier est vide. Pas de commande enregistree';
		}
	}








//// do not seam to be used
function envoyerMail($idAdherent, $totalTTC)
		{
		/************* ENVOI MAIL ****************/
$date = date("d-m-Y");
ini_set("SMTP","smtp.free.fr");

$mail = stripslashes(SelectionMailAdherentAD($idAdherent)); // Déclaration de l'adresse de destination.
if (!preg_match("#^[a-z0-9._-]+@(hotmail|live|msn).[a-z]{2,4}$#", $mail)) // On filtre les serveurs qui rencontrent des bogues.
{
	$passage_ligne = "\r\n";
}
else
{
	$passage_ligne = "\n";
}
//=====Déclaration des messages au format texte et au format HTML.
/*
$message_txt = "Salut à tous, voici un e-mail envoyé par un script PHP.";
$message_html = "<html><head></head><body><b>Salut à tous</b>, voici un e-mail envoyé par un <i>script PHP</i>.</body></html>";
*/

/* Message texte */
$message_txt = "Vos achats du " . $date . "\n"
;
for($i = 0; $i < count($_SESSION['inde_panier']['nomReference']); $i++)
{
	$message_txt = $message_txt . stripslashes($_SESSION['inde_panier']['nomReference'][$i]) . "  [ " . round($_SESSION['inde_panier']['prixReference'][$i],2) . " euros ]\n"; 	 	
}
$message_txt = $message_txt . "\nTOTAL TTC : " . round($totalTTC,2) . " euros.\n"; 
$message_txt = $message_txt . "Le solde de votre compte MoneyCoop est maintenant de : " . round($nouveauSolde,2) . " euros.\n";
$message_txt = $message_txt . "Merci.";

/* Message html */
$message_html = "<html><head></head><body>";
$message_html = $message_html . "Vos achats du " . $date . "<br /><br />";

for($i = 0; $i < count($_SESSION['inde_panier']['nomReference']); $i++)
{
	$message_html = $message_html . $_SESSION['inde_panier']['qteReference'][$i] . " " . stripslashes($_SESSION['inde_panier']['nomReference'][$i]) . "  [ " . round($_SESSION['inde_panier']['prixReference'][$i],2) . " euros ]<br />"; 	
}

$message_html = $message_html . "<br />TOTAL TTC : " . round($totalTTC,2) . " euros.<br />"; 
$message_html = $message_html . "Le solde de votre compte MoneyCoop est maintenant de : " . round($nouveauSolde,2) . " euros.<br />";
$message_html = $message_html . "Merci.";

$message_html = $message_html . "</body></html>";


//==========
 
//=====Création de la boundary
$boundary = "-----=".md5(rand());
//==========
 
//=====Définition du sujet.
$sujet = "[L INDEPENDANTE]Ticket achats";
//=========
 
//=====Création du header de l'e-mail.
$header = "From: \"L INDEPENDANTE\"<coop.lindependante@orange.fr>".$passage_ligne;
$header.= "Reply-to: \"L INDEPENDANTE\"<coop.lindependante@orange.fr>".$passage_ligne;
$header.= "MIME-Version: 1.0".$passage_ligne;
$header.= "Content-Type: multipart/alternative;".$passage_ligne." boundary=\"$boundary\"".$passage_ligne;
//==========
 
//=====Création du message.
$message = $passage_ligne."--".$boundary.$passage_ligne;
//=====Ajout du message au format texte.
$message.= "Content-Type: text/plain; charset=\"ISO-8859-1\"".$passage_ligne;
$message.= "Content-Transfer-Encoding: 8bit".$passage_ligne;
$message.= $passage_ligne.$message_txt.$passage_ligne;
//==========
$message.= $passage_ligne."--".$boundary.$passage_ligne;
//=====Ajout du message au format HTML
$message.= "Content-Type: text/html; charset=\"ISO-8859-1\"".$passage_ligne;
$message.= "Content-Transfer-Encoding: 8bit".$passage_ligne;
$message.= $passage_ligne.$message_html.$passage_ligne;
//==========
$message.= $passage_ligne."--".$boundary."--".$passage_ligne;
$message.= $passage_ligne."--".$boundary."--".$passage_ligne;
//==========
 
//=====Envoi de l'e-mail.
mail($mail,$sujet,$message,$header);
//==========
/*****************************************/
	}	
	
?>
