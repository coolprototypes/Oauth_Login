<?php
require_once 'server-libs/twitteroauth/twitteroauth.php';
require_once 'config.php';
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
$authUrl = $gClient->createAuthUrl();
if (isset($_POST['twitter'])) {

    $twitteroauth = new TwitterOAuth(CONSUMER_KEY, CONSUMER_TOKEN);
    $request_token = $twitteroauth
            ->getRequestToken(REDIRECT_URL);
    $_SESSION['oauth_token'] = $request_token['oauth_token'];
    $_SESSION['oauth_token_secret'] = $request_token['oauth_token_secret'];

    if ($twitteroauth->http_code == 200) {
        // Let's generate the URL and redirect
        $url = $twitteroauth->getAuthorizeURL($request_token['oauth_token']);
        header('Location: ' . $url);
    } else {
        die('Something wrong happened.');
    }
}
if (isset($_POST['fbpost'])) {
    $_SESSION['fbpost'] = $_POST['fbpost'];
    header('Location: ' . REDIRECT_URL);
}
?>
<!Doctype html>
<html>
    <head>
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <meta charset="utf-8">
        <title>Jaagar-Login</title>
        <link rel="stylesheet" href="assets/css/bootstrap.css"/>
        <link rel="stylesheet" href="assets/css/font-awesome.css"/>
        <script src="assets/js/login.js"></script>
    </head>
    <body>
        <div class="login-ctnr">
            <p>Please login</p>
            <div>
                <a href="#" onclick="tweet_login();"
                   class="btn btn-block btn-social btn-lg btn-twitter">
                    <i class="fa fa-twitter"></i> Sign in with Twitter</a>
                <form id="tweetForm" action="login.php" method="POST" style="display: none;">
                    <input type="text" value="twitter" name="twitter" />
                </form>
            </div>
            <div>
                <a class="btn btn-block btn-social btn-lg btn-google-plus" 
                   href="<?php echo $authUrl ?>">
                    <i class="fa fa-google-plus"></i> Sign in with google</a>
            </div>
            <div>
                <a href="#" onclick="fb_login();"
                   class="btn btn-block btn-social btn-lg btn-facebook">
                    <i class="fa fa-facebook"></i> Sign in with facebook</a>
                <form id="fbForm" action="login.php" method="POST" style="display: none;">
                    <input type="text" value="" name="fbpost" id="fbpost"/>
                </form>
            </div>
        </div>
    </body>
</html>