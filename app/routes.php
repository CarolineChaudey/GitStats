<?php

	// accueil
	$app->get('/', function () use ($app, $_ENV) {
		return $app['twig']->render('recherche.html.twig');
	})
	->bind('accueil');


	// après saisie des mots clefs
	$app->get('/recherche/', function () use ($app, $_ENV, $_GET) {
		$recherche = $_GET["motsClefs"];

		// si rien n'est saisi en entrée
		if($recherche == ""){
			return $app->redirect($app['url_generator']->generate('accueil'));
		}

		$listeMots = explode(" ", $recherche);
		$recherche = implode("+", $listeMots);

		$id_session = curl_init();
		$requete = 'https://api.github.com/search/repositories?q='.$recherche.'&per_page=100';
		curl_setopt($id_session, CURLOPT_URL, $requete);
		curl_setopt($id_session, CURLOPT_USERAGENT, 'Mozilla/5.0 (X11; Ubuntu; Linux x86_64; rv:39.0) Gecko/20100101 Firefox/39.0');
		curl_setopt($id_session, CURLOPT_RETURNTRANSFER, true);
		$resultat = curl_exec($id_session);

		// décodage et stockage de la liste des repositories
		$listeRepos = json_decode($resultat, true);
		$app['session']->set('listeRepos', $listeRepos);
		return $app['twig']->render('recherche.html.twig');
	})
	->bind('rechercherRepos');


	// récupération des stats
	$app->get('/stats/{fullname}', function ($fullname) use ($app, $_ENV, $_GET) {

		// recherche des 100 derniers commits
		$id_session = curl_init();
		$requete = 'https://api.github.com/repos/'.$fullname.'/commits?&per_page=100';
		curl_setopt($id_session, CURLOPT_URL, $requete);
		curl_setopt($id_session, CURLOPT_USERAGENT, 'Mozilla/5.0 (X11; Ubuntu; Linux x86_64; rv:39.0) Gecko/20100101 Firefox/39.0');
		curl_setopt($id_session, CURLOPT_RETURNTRANSFER, true);
		$resultatCommits = curl_exec($id_session);

		$listeCommits = json_decode($resultatCommits, true);

		// on répertorie les committers et leur nb de participations
		
		$listeCollab = array();
		$collab = "";
		
		foreach ($listeCommits as $commit) {
			
			$collab = $commit["committer"]["id"];
			if(isset($listeCollab[$collab])){
				$listeCollab[$collab]["nb"] ++;
			}
			else{
				$listeCollab[$collab]["nb"] = 1;
				$listeCollab[$collab]["login"] = $commit["committer"]["login"];
			}

		}
		
		// on convertie le nb de participations en %
		$nbCommits = sizeof($listeCommits);
		$pourcent = 0;
		foreach ($listeCollab as $unCollab) {
			$id = array_search($unCollab, $listeCollab);
			$listeCollab[$id]["nb"] = round( ($listeCollab[$id]["nb"] / $nbCommits *100), 2 );
		}


		return $app['twig']->render('stats.html.twig', array('listeCommits' => $listeCommits, 'listeCollab' => $listeCollab));
	})
	->assert('fullname', '.+')
	->bind('statsRepo');

?>
