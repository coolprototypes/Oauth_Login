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
    print_r($request_token);
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
?>
<!Doctype html>
<html>
    <head>
        <title>Hello</title>
    </head>
    <body>
        <form action="login.php" method="POST">
            <input type="submit" value="twitter" name="twitter" />
        </form>
        <div>
            <fb:login-button scope="public_profile,email" onlogin="checkLoginState();"
                             class="fb-login-button" data-max-rows="1" data-size="large"
                             data-show-faces="false" data-auto-logout-link="false">
            </fb:login-button>
        </div>
        <a class="login" href="<?php echo $authUrl ?>">Google Login link</a>
        <script>
            var viaBtnClick = false;
            var userObj = false;
            function statusChangeCallback(response) {
                console.log('statusChangeCallback');
                console.log(response);
                if (response.status === 'connected') {
                    getInfo();
                } else if (response.status === 'not_authorized') {
                } else {
                }
            }
            function checkLoginState() {
                if (userObj) {
                    postData();
                } else {
                    viaBtnClick = true;
                    FB.getLoginStatus(function(response) {
                        statusChangeCallback(response);
                    });
                }
            }

            window.fbAsyncInit = function() {
                FB.init({
                    appId: '1450285645237516',
                    cookie: true, // enable cookies to allow the server to access 
                    xfbml: true, // parse social plugins on this page
                    version: 'v2.0' // use version 2.0
                });
                FB.getLoginStatus(function(response) {
                    statusChangeCallback(response);
                });

            };

            (function(d, s, id) {
                var js, fjs = d.getElementsByTagName(s)[0];
                if (d.getElementById(id))
                    return;
                js = d.createElement(s);
                js.id = id;
                js.src = "//connect.facebook.net/en_US/sdk.js";
                fjs.parentNode.insertBefore(js, fjs);
            }(document, 'script', 'facebook-jssdk'));

            function getInfo() {
                console.log('Welcome!  Fetching your information.... ');
                FB.api('/me', function(response) {
                    console.log(JSON.stringify(response));
                    console.log('Successful login for: ' + response.name);
                    if (viaBtnClick) {
                        FB.logout(function(response) {
                        });
                        postData();
                    }
                    userObj = response;
                });
            }
            function postData() {
                values = {'request': JSON.stringify(userObj)};
                postToPath('auth_handle.php', values);
            }
            function postToPath(path, params, method) {
                method = method || "post"; // Set method to post by default if not specified.

                // The rest of this code assumes you are not using a library.
                // It can be made less wordy if you use one.
                var form = document.createElement("form");
                form.setAttribute("method", method);
                form.setAttribute("action", path);

                for (var key in params) {
                    if (params.hasOwnProperty(key)) {
                        var hiddenField = document.createElement("input");
                        hiddenField.setAttribute("type", "hidden");
                        hiddenField.setAttribute("name", key);
                        hiddenField.setAttribute("value", params[key]);

                        form.appendChild(hiddenField);
                    }
                }

                document.body.appendChild(form);
                form.submit();
            }
        </script>
    </body>
</html>