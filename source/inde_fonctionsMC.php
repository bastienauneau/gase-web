<?php
	function ConnexionBDD_MC()
	{
		if(!$connexion)
		{	
			$connection = mysql_connect("localhost", "root", "Rouss7tte") or die(mysql_error());
			mysql_select_db("gasedl") or die(mysql_error());
		}	
	}
	
	function FermerConnexionBDD_MC($connexion)
	{
		mysql_close($connection);
	}

	
	function SelectionSoldeAdherentMC($idAdherent)
	{
		$connexion = ConnexionBDD_MC();

		$result = mysql_query("SELECT SOLDE FROM _inde_COMPTES WHERE ID_ADHERENT='$idAdherent' AND DATE = (SELECT MAX(DATE) FROM _inde_COMPTES WHERE ID_ADHERENT= '$idAdherent')");
		while ( $row = mysql_fetch_array($result))
		{
			$solde = $row[SOLDE];
		}		
		
		FermerConnexionBDD_MC($connexion);
		
		return $solde;
	}
	
	function SelectionVersementsMC($idAdherent)
	{
		$connexion = ConnexionBDD_MC();

		$result = mysql_query("SELECT MONTANT,DATE FROM _inde_COMPTES WHERE ID_ADHERENT='$idAdherent' AND OPERATION = 'APPROVISIONNEMENT' UNION SELECT -MONTANT,DATE FROM _inde_COMPTES WHERE ID_ADHERENT='$idAdherent' AND OPERATION = 'DEPENSE' ORDER BY 2 DESC ");

		while ( $row = mysql_fetch_array($result))
		{
			$tabVersements[$row[DATE]] = $row[MONTANT];
		}
		
		FermerConnexionBDD_MC($connexion);
		
		return $tabVersements;
	}
	
	function ApprovisionnementMC($idAdherent, $somme)
	{
		$connexion = ConnexionBDD_MC();
		
		$nouveauSolde = SelectionSoldeAdherentMC($idAdherent) + $somme;

		$nouveauSolde = str_replace(",", ".", $nouveauSolde);
		$somme = str_replace(",", ".", $somme);

		$requete = "INSERT INTO _inde_COMPTES (ID_ADHERENT, SOLDE, DATE, OPERATION, MONTANT) values('$idAdherent','$nouveauSolde',NOW(),'APPROVISIONNEMENT','$somme')";
		mysql_query($requete);		

		FermerConnexionBDD_MC($connexion);
	}
	
	
	function DepenseMC($idAdherent, $somme)
	{
		$connexion = ConnexionBDD_MC();
		
		$nouveauSolde = SelectionSoldeAdherentMC($idAdherent) - $somme;

		$nouveauSolde = str_replace(",", ".", $nouveauSolde);
		$somme = str_replace(",", ".", $somme);

		$requete = "INSERT INTO _inde_COMPTES (ID_ADHERENT, SOLDE, DATE, OPERATION, MONTANT) values('$idAdherent','$nouveauSolde',NOW(),'DEPENSE','$somme')";
		mysql_query($requete);	

		FermerConnexionBDD_MC($connexion);
	}
	
?>
