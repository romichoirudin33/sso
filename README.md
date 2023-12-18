# Single Sign On NTBPROV

Laravel wrapper OAuth 2 libraries to make support for (NTB Goverments).

## Instalation

To get started SSO, use the Composer package manager to add the package to your project's dependencies:

```
composer require romichoirudin33/sso
```

after install composer, please insert `SsoNtbServiceProvider` on `config/app.php`

```
/*
  * Application Service Providers...
*/
Romichoirudin33\Sso\SsoNtbServiceProvider::class,
```

then please insert `config/services.php`

```
'ntbprov' => [
  'client_id' => env('SSO_CLIENT_ID'),
  'client_secret' => env('SSO_CLIENT_SECRET'),
  'redirect' => env('SSO_CLIENT_REDIRECT'),
],
```

and yey, finally you must insert that **SSO_CLIENT** in `.env` example like this

```
SSO_CLIENT_ID=xxxxxxx-xxxx-xxxx-xxxxx-xxxxxxxx
SSO_CLIENT_SECRET=xxxxxxxxxxxxxxxx
SSO_CLIENT_REDIRECT=https://example.ntbprov.go.id/auth/callback
```


For get environment `SSO_CLIENT` you must register your application on https://sso.ntbprov.go.id.

If you don't have account *DEVELOPER* on https://sso.ntbprov.go.id, you can send email to diskominfotik@ntbprov.go.id for get more information or contact us on https://layanan.diskominfotik.ntbprov.go.id/.

## Authentication Using Laravel

### Routing

To authenticate users using an OAuth provider, you will need two routes: one for redirecting the user to the OAuth provider, and another for receiving the callback from the provider after authentication. The example routes below demonstrate the implementation of both routes:

```
use Romichoirudin33\Sso\Facades\Sso;

Route::get('/auth/redirect', function () {
    return Sso::driver('ntbprov')->redirect();
});

Route::get('/auth/callback', function () {
    $user = Sso::driver('ntbprov')->user();

    // $user->token
});
```

### Authentication & Storage

Once the user has been retrieved from the OAuth provider, you may determine if the user exists in your application's database and authenticate the user. If the user does not exist in your application's database, you will typically create a new record in your database to represent the user:

```
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Romichoirudin33\Sso\Facades\Sso;

Route::get('/auth/callback', function () {
    $ssoUser = Sso::driver('ntbprov')->user();

    $user = User::updateOrCreate([
        'email' => $ssoUser->email,
    ], [
        'name' => $ssoUser->name,
    ]);

    Auth::login($user);

    return redirect('/dashboard');
});
```

### User Details

After the user is redirected back to your application's authentication callback route, you may retrieve the user's details using Socialite's user method. The user object returned by the user method provides a variety of properties and methods you may use to store information about the user in your own database.

```
use Romichoirudin33\Sso\Facades\Sso;

Route::get('/auth/callback', function () {
    $user = Sso::driver('ntbprov')->user();

    // OAuth 2.0 providers...
    $token = $user->token;
    $refreshToken = $user->refreshToken;
    $expiresIn = $user->expiresIn;

});
```

### User Details (JSON)
```
{
    "id": 13819,
    "name": "ROMI CHOIRUDIN",
    "email": "romi@ntbprov.go.id",
    "email_verified_at": "2023-12-11T08:13:35.000000Z",
    "nip": "199412032023211011",
    "created_at": "2023-12-11T08:33:16.000000Z",
    "updated_at": "2023-12-11T08:33:16.000000Z"
}
```

#
## Custom Authentication Using Laravel

Actually the application don't have register feature from user. Because some application have limit user to access.
User can access just user that register from administrator. So if your application like this, you must custom authentication, example like this

### Routes
```
Route::get('auth/redirect', [\App\Http\Controllers\Auth\SingleSignOnController::class, 'redirect'])->name('auth-redirect');
Route::get('auth/callback', [\App\Http\Controllers\Auth\SingleSignOnController::class, 'callback']);
```

### Controllers
```
<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Romichoirudin33\Sso\Facades\Sso;

class SingleSignOnController extends Controller
{
    public function redirect()
    {
        return Sso::driver('ntbprov')->redirect();
    }

    public function callback()
    {
        $ssoUser = Sso::driver('ntbprov')->user();

        $user = User::where('email', $ssoUser->email)->first();
        if ($user) {
            $user->name = $ssoUser->name;
            $user->email_verified_at = $ssoUser->email_verified_at;
            $user->save();

            Auth::login($user);

            return redirect('home');
        } else {
            return view('auth.access-denied', [
                'user' => $ssoUser
            ]);
        }
    }
}
```

### Views
```
<!doctype html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport"
          content="width=device-width, user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Title Document</title>
</head>
<body>
Your account not yet registered from administrator, Please contact administrator for registered account
</body>
</html>
```

But it's just example for using this package. You still need to adapt the code to your needs.

## Custom Authentication Using CodeIgniter 3

### HTTPClient Libraries
Please make libraries HTTPClient on **application/libraries/HTTPClient.php**  
```
<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class HTTPClient {
  protected $CI;

  public function __construct() {
    $this->CI =& get_instance();
  }

  public function get($url, $params = array(), $headers = array()) {
    $url = $url . '?' . http_build_query($params);
    $ch = curl_init($url);
    curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
    $response = curl_exec($ch);
    curl_close($ch);
    return $response;
  }

  public function post($url, $data = array()) {
    $ch = curl_init($url);
    curl_setopt($ch, CURLOPT_POST, 1);
    curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($data));
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
    $response = curl_exec($ch);
    curl_close($ch);
    return $response;
  }

  public function generateRandomString($length = 10) {
    $characters = 'abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789';
    $randomString = '';
    for ($i = 0; $i < $length; $i++) {
      $randomString .= $characters[rand(0, strlen($characters) - 1)];
    }
    return $randomString;
  }
}
```

### Controller
Don't forget to autoload libraries and helpers before make controller
```
$autoload['libraries'] = array('session');
$autoload['helper'] = array('url');
```

So you can use this code for get data user, but if you have specific logic authentication 
you still need to adapt the code to your needs.

```
<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Auth extends CI_Controller {

  public function __construct() {
    parent::__construct();
    $this->load->library('HTTPClient');
  }

  var $clientId = '9adfebe4-1ded-425c-ad2d-892d5a6c4ccc';
  var $clientSecret = 'TcdmxRTOwdF7cs26PLbRHs9tUSbrmGCNLYcYrVtQ';
  var $redirectUrl = 'http://localhost/codeigniter/index.php/welcome/callback2';

  public function redirect() {
    $state = $this->httpclient->generateRandomString(40);
    $this->session->set_userdata('state', $state);
 
    $query = http_build_query([
      'client_id' => $this->clientId,
      'redirect_uri' => $this->redirectUrl,
      'response_type' => 'code',
      'scope' => '',
      'state' => $state,
    ]);
 
    return redirect('https://sso.ntbprov.go.id/oauth/authorize?'.$query);
  }

  public function callback(){
    $code = $this->input->get('code');
    $data = [
      'grant_type' => 'authorization_code',
      'client_id' => $this->clientId,
      'client_secret' => $this->clientSecret,
      'code' => $code,
      'redirect_uri' => $this->redirectUrl,
    ];

    $url = 'https://sso.ntbprov.go.id/oauth/token';
    $response = $this->httpclient->post($url, $data);
    $response = json_decode($response);

    $token = $response->access_token;
    $header = array('Authorization: Bearer '.$token, 'Content-Type: application/json', 'Accept: application/json');
    $user = $this->httpclient->get('https://sso.ntbprov.go.id/api/user', [], $header);

    /*
    * This from get data user
    * If you want to logic authentication you still need to adapt the code to your needs
    */
  }
}

```


## Supports Me
For next information about this package you can contact me on romi@ntbprov.go.id
