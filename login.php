<?php
require 'openid.php'; // la librairie LightOpenID doit être dans le même dossier

$openid = new LightOpenID('file:///C:/Users/matis/Desktop/baldwinrp-site/index.html'); // Remplace par ton domaine, par exemple 'localhost' pour tests locaux

if (!$openid->mode) {
    // On lance la connexion Steam
    $openid->identity = 'https://steamcommunity.com/openid';
    header('Location: ' . $openid->authUrl());
    exit;
} elseif ($openid->mode == 'cancel') {
    echo 'Connexion annulée.';
} else {
    if ($openid->validate()) {
        $id = $openid->identity;
        // Récupération du SteamID64 via regex
        preg_match('/^https:\/\/steamcommunity\.com\/openid\/id\/(\d+)$/', $id, $matches);
        $steamID64 = $matches[1];

        // Appel API Steam pour récupérer le pseudo
        $apikey = 'F5B6717218172A9232790359EE00DF65'; // Ta clé Steam Web API (https://steamcommunity.com/dev/apikey)
        $url = "https://api.steampowered.com/ISteamUser/GetPlayerSummaries/v2/?key=$apikey&steamids=$steamID64";

        $json = file_get_contents($url);
        $data = json_decode($json, true);

        if (isset($data['response']['players'][0])) {
            $player = $data['response']['players'][0];
            $pseudo = $player['personaname'];
            $avatar = $player['avatarfull'];

            echo "<h1>Bienvenue, $pseudo !</h1>";
            echo "<img src='$avatar' alt='Avatar Steam'>";
            echo "<p>Votre SteamID64 : $steamID64</p>";
            echo '<p><a href="index.html">Retour à l\'accueil</a></p>';
        } else {
            echo "Impossible de récupérer les informations Steam.";
        }
    } else {
        echo "Échec de la validation Steam OpenID.";
    }
}
?>
