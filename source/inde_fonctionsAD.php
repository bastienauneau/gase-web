<?php
	function ConnexionBDD_AD()
	{
		if(!$connexion)
		{	
			$connection = mysql_connect("localhost", "root", "Rouss7tte") or die(mysql_error());
			mysql_select_db("gasedl") or die(mysql_error());
		}	
	}
	
	function FermerConnexionBDD_AD($connexion)
	{
		mysql_close($connection);
	}

	function EnregistrerNouvelAdherent($nom, $prenom, $mail, $telephone_fixe, $telephone_portable, $adresse, $commentaire, $ticket, $visible)
	{
		$connexion = ConnexionBDD_AD();

		$requete = "INSERT INTO _inde_ADHERENTS (NOM, PRENOM, MAIL, TELEPHONE_FIXE, TELEPHONE_PORTABLE, ADRESSE, COMMENTAIRE, TICKET_CAISSE, DATE_INSCRIPTION, VISIBLE) values('$nom','$prenom','$mail','$telephone_fixe','$telephone_portable', '$adresse', '$commentaire', '$ticket', NOW(),'$visible')";
		mysql_query($requete);
		
		$result = mysql_query("SELECT MAX(ID_ADHERENT) FROM _inde_ADHERENTS");
		while ( $row = mysql_fetch_array($result))
		{
			$idAdherentMax = $row[0];
		}
		
		$requete = "INSERT INTO _inde_COMPTES (ID_ADHERENT, SOLDE, DATE, OPERATION, MONTANT) values('$idAdherentMax',0,NOW(),'CREATION',0)";
		mysql_query($requete);
		
		FermerConnexionBDD_AD($connexion);
	}
	
	function SelectionListeAdherents()
	{
		$connexion = ConnexionBDD_AD();

		$result = mysql_query("SELECT ID_ADHERENT, NOM FROM _inde_ADHERENTS ORDER BY NOM");
		while ( $row = mysql_fetch_array($result))
		{
			$listeAdherents[$row[ID_ADHERENT]] = $row[NOM];
		}
		
		FermerConnexionBDD_AD($connexion);
		
		return $listeAdherents;
	}
	
	function SelectionDonneesAdherent($idAdherent)
	{
		$connexion = ConnexionBDD_AD();

		$result = mysql_query("SELECT NOM, PRENOM, MAIL, TELEPHONE_FIXE, TELEPHONE_PORTABLE, ADRESSE, COMMENTAIRE, TICKET_CAISSE, DATE_INSCRIPTION, VISIBLE FROM _inde_ADHERENTS WHERE ID_ADHERENT = '$idAdherent'");
		while ( $row = mysql_fetch_array($result))
		{		
			$donnees['NOM'] = $row[0];
			$donnees['PRENOM'] = $row[1];
			$donnees['MAIL'] = $row[2];
			$donnees['TELEPHONE_FIXE'] = $row[3];
			$donnees['TELEPHONE_PORTABLE'] = $row[4];
			$donnees['ADRESSE'] = $row[5];
			$donnees['COMMENTAIRE'] = $row[6];
			$donnees['TICKET_CAISSE'] = $row[7];
			$donnees['DATE_INSCRIPTION'] = $row[8];
			$donnees['VISIBLE'] = $row[9];
		}

		FermerConnexionBDD_AD($connexion);
		
		return $donnees;
	}
	
	function MajAdherent($idAdherent, $nom, $prenom, $email, $telephone_fixe, $telephone_portable, $adresse, $commentaire, $ticket, $visible)
	{
		$connexion = ConnexionBDD_AD();

		$requete = "UPDATE _inde_ADHERENTS SET NOM = '$nom', PRENOM = '$prenom', MAIL='$email', TELEPHONE_FIXE = '$telephone_fixe', TELEPHONE_PORTABLE = '$telephone_portable', ADRESSE = '$adresse', COMMENTAIRE = '$commentaire', TICKET_CAISSE = '$ticket', VISIBLE = '$visible' WHERE ID_ADHERENT = '$idAdherent'";
		mysql_query($requete);

		FermerConnexionBDD_AD($connexion);
	}
	
	function SelectionListeAD()
	{
		$connexion = ConnexionBDD_AD();

		$compteur = 0;
		
		$result = mysql_query("SELECT ID_ADHERENT, NOM, PRENOM FROM _inde_ADHERENTS ORDER BY NOM");
		while ( $row = mysql_fetch_array($result))
		{		
			$donnees['ID_ADHERENT'] = $row[0];
			$donnees['NOM'] = $row[1];
			$donnees['PRENOM'] = $row[2];
			
			$listeAdherents[$compteur] = $donnees;
			$compteur++;
		}
				
		FermerConnexionBDD_AD($connexion);
		
		return $listeAdherents;
	}
	
	function SelectionListeActifsAD()
	{
		$connexion = ConnexionBDD_AD();

		$compteur = 0;
		
		$result = mysql_query("SELECT ID_ADHERENT, NOM, PRENOM FROM _inde_ADHERENTS WHERE VISIBLE = 1 ORDER BY NOM");
		while ( $row = mysql_fetch_array($result))
		{		
			$donnees['ID_ADHERENT'] = $row[0];
			$donnees['NOM'] = $row[1];
			$donnees['PRENOM'] = $row[2];
			
			$listeAdherents[$compteur] = $donnees;
			$compteur++;
		}
				
		FermerConnexionBDD_AD($connexion);
		
		return $listeAdherents;
	}
	
	function SelectionPrenomNomAdherent($idAdherent)
	{
		$connexion = ConnexionBDD_AD();

		$result = mysql_query("SELECT PRENOM, NOM FROM _inde_ADHERENTS WHERE ID_ADHERENT = '$idAdherent'");
		while ( $row = mysql_fetch_array($result))
		{
			$prenomAdherent = $row[PRENOM];
			$nomAdherent = $row[NOM];
		}
		
		FermerConnexionBDD_AD($connexion);
		
		return $prenomAdherent.' '.$nomAdherent.' ';
	}
	
	function SelectionListeAchatsAdherent($idAdherent)
	{
		$connexion = ConnexionBDD_AD();

		$compteur = 0;
		
		$result = mysql_query("SELECT c.ID_ACHAT, c.TOTAL_TTC, c.NB_REFERENCES, c.DATE_ACHAT FROM _inde_ACHATS c WHERE c.ID_ADHERENT = '$idAdherent' ORDER BY c.DATE_ACHAT DESC");
		while ( $row = mysql_fetch_array($result))
		{		
			$ligne['ID_ACHATS'] = $row[0];
			$ligne['MONTANT'] = $row[1];
			$ligne['NB_ARTICLES'] = $row[2];
			$ligne['DATE_ACHATS'] = $row[3];
			
			$listeCde[$compteur] = $ligne;
			$compteur++;
		}

		FermerConnexionBDD_AD($connexion);
		
		return $listeCde;
	}
		
	function SelectionMailAdherentAD($idAdherent)
	{
		$connexion = ConnexionBDD_AD();

		$result = mysql_query("SELECT MAIL FROM _inde_ADHERENTS WHERE ID_ADHERENT= '$idAdherent'");
		$row = mysql_fetch_array($result);
		$mail = $row[MAIL];

		FermerConnexionBDD_AD($connexion);
		
		return $mail;
	}	
?>
