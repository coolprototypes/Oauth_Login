var viaBtnClick = false;
var userObj = false;
function statusChangeCallback(response) {
    if (response.status === 'connected') {
        getInfo();
    } else if (response.status === 'not_authorized') {
    } else {
    }
}

function fb_login() {
    if (userObj) {
        postData();
    } else {
        FB.login(function(response) {
            viaBtnClick = true;
            if (response.authResponse) {
                access_token = response.authResponse.accessToken; //get access token
                user_id = response.authResponse.userID; //get FB UID
                getInfo();
            } else {
                //user hit cancel button
                console.log('User cancelled login or did not fully authorize.');
            }
        }, {
            scope: 'publish_stream,email'
        });
    }
}

window.fbAsyncInit = function() {
    FB.init({
        appId: FB_APP_ID,
        cookie: true, // enable cookies to allow the server to access 
        xfbml: true, // parse social plugins on this page
        version: 'v2.0', // use version 2.0
        oauth: true
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
    FB.api('/me', function(response) {
        userObj = response;
        if (viaBtnClick) {
            FB.logout(function(response) {
                postData();
            });
        }
    });
}
function postData() {
    values = "fb-post=" + JSON.stringify(userObj);
//    postToPath('auth_handle.php', values);
    postToPath('auth_handle.php', JSON.stringify(userObj));
    viaBtnClick = false;
    userObj = false;
}
function postToPath(path, params, method) {
    document.getElementById('fbpost').value = params;
    document.getElementById('fbForm').submit();

}
function tweet_login() {
    document.getElementById('tweetForm').submit();
}