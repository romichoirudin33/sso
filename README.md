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
    $ssoUser = Sso::driver('github')->user();

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

## Supports Me
For next information about this package you can contact me on romi@ntbprov.go.id
