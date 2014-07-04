<?php

require_once 'server-libs/twitteroauth/twitteroauth.php';
require_once 'config.php';
require_once 'LoginDbConnect.php';
require_once 'server-libs/gplus/Google_Client.php';
require_once 'server-libs/gplus/contrib/Google_Oauth2Service.php';

session_start();
$gClient = new Google_Client();
$gClient->setApplicationName("Google login app");
$gClient->setClientId(GPLUS_CLIENT_ID);
$gClient->setClientSecret(GPLUS_CLIENT_SECRET);
$gClient->setRedirectUri(REDIRECT_URL);
$gClient->setDeveloperKey(DEVELOPER_KEY);
$google_oauthV2 = new Google_Oauth2Service($gClient);

if (!empty($_GET['oauth_token']) &&
        !empty($_GET['oauth_verifier'])) {
    $twitteroauth = new TwitterOAuth(CONSUMER_KEY, CONSUMER_TOKEN
            , $_SESSION['oauth_token'], $_SESSION['oauth_token_secret']);
    $access_token = $twitteroauth->getAccessToken($_GET['oauth_verifier']);
    $user_info = $twitteroauth->get('account/verify_credentials');
    $user = array('oauth_provider' => 'twitter', 'oauth_uid' => $user_info->id,
        'username' => $user_info->screen_name, 'oauth_token' => $access_token['oauth_token'],
        'oauth_secret' => $access_token['oauth_token_secret']);
    unset_sessions(array('oauth_token', 'oauth_token_secret'));
} else if (isset($_GET['code'])) {
    $gClient->authenticate($_GET['code']);
    $authToken = $gClient->getAccessToken();
    $gClient->setAccessToken($authToken);
    $user_info = $google_oauthV2->userinfo->get();
    $user = array('oauth_provider' => 'gplus', 'oauth_uid' => $user_info['id'],
        'username' => $user_info['name'], 'oauth_token' => 'FALSE',
        'oauth_secret' => 'FALSE');
    $gClient->revokeToken();
} else if (isset($_SESSION['fbpost'])) {
    $user_info = json_decode($_SESSION['fbpost'], TRUE);
    unset($_SESSION['fbpost']);
    $user = array('oauth_provider' => 'facebook', 'oauth_uid' => $user_info['id'],
        'username' => $user_info['name'], 'oauth_token' => 'FALSE',
        'oauth_secret' => 'FALSE');
} else {
    header('Location: ' . LOGOUT_PAGE);
}

if (isset($user_info->error)) {
    // Something's wrong, go back to square 1
    header('Location: ' . LOGOUT_PAGE);
} else {
    // Let's find the user by its ID
    $conn = new LoginDbConnect();
    $db = $conn->connect();

    $result = $db->users()
            ->where('oauth_provider = ? AND oauth_uid = ?', $user['oauth_provider'], $user['oauth_uid'])
            ->fetch();

    // If not, let's add it to the database
    if (empty($result)) {
        $result = $db->users->insert($user);
    } else {
        // Update the tokens
        $userRow = $db->users()
                ->where('oauth_provider = ? AND oauth_uid = ?', $user['oauth_provider'], $user['id']);
        $row = array('oauth_token' => $access_token['oauth_token'],
            'oauth_secret' => $access_token['oauth_token_secret']);
        $userRow->update($row);
    }

    $_SESSION['id'] = $user['oauth_uid'];
    $_SESSION['username'] = $result['username'];
    if (!empty($_SESSION['username'])) {
        // User is logged in, redirect
        header('Location: ' . URL_LOGGED_IN);
    }
}

function unset_sessions($sessionVarArray) {
    for ($n = 0; $n < sizeof($sessionVarArray); $n++) {
        unset_sessions($_SESSION[$sessionVarArray[$n]]);
    }
}
