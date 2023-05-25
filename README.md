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

#

## Configuration

For SSO NTBProv please insert `config/services.php`

```
'ntbprov' => [
  'client_id' => env('SSO_CLIENT_ID'),
  'client_secret' => env('SSO_CLIENT_SECRET'),
  'redirect' => env('SSO_CLIENT_REDIRECT'),
],
```

and yey, you must to insert that **SSO_CLIENT** in `.env`

For get **SSO_CLIENT** you can make on https://sso.ntbprov.go.id. if you dont have account and you want to make SSO (Single Sign On) for your application, you can send email to kominfotik@ntbprov.go.id for get more information.

# Authentication

## Routing

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

## Authentication & Storage

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

## User Details

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

## Supports Me
