# Update

There is now a module for creation of a REST API in ProcessWire: https://github.com/thomasaull/RestApi Support and Updates for this site profile will be discontinued from now on

# RestApiProfile
Build a rest API with ProcessWire. Including JWT-Auth and a Vue SPA example

**Disclaimer**

This is an example, there is no guarantee this code is secure! Use at your own risk and/or send me PRs with improvements.

**Credits…**

…go to [Benjamin Milde](https://github.com/LostKobrakai) for his code example on how to use FastRoute with ProcessWire and [Camilo Castro](https://gist.github.com/clsource) for this [Gist](https://gist.github.com/clsource/dc7be74afcbfc5fe752c)

### Install

Grab a copy of processwire and place the site-restapi directory in the root of your ProcessWire directory. Install ProcessWire as usual (don’t forget to pick the site profile).

Then:

If you have composer installed run the following commands:
```
composer require nikic/fast-route
composer require firebase/php-jwt
```

Alternatively, you can grab the /vendor folder over here: https://github.com/thomasaull/RestApiProfile-Src

The Rest-API should work now. To check you can use [Postman](https://www.getpostman.com/) or [Insomnia](https://insomnia.rest/) and run a GET Request:

`http://your-dev-host.dev/api/test`

You should get the following error:

```
{
  "error": "No Authorization Header found"
}
```

Because you’re not authenticated yet. To disable authentication, go to /site/templates/api/Router.php and in the function *handle* set the variable $authActive to false for now.

If you run the same Request again, you’ll get the following
```
{
  "user": "guest"
}
```

To use JWT-Auth you have to send a GET Request to http://yourhost/api/auth with two parameters, username and password. The API will log your user in and return you the JWT-Token, which you have to add to every following request.

An example for a simple login form is implemented as a Vue SPA based on the [Vue Webpack Template](https://github.com/vuejs-templates/webpack). To install, go to /site/templates/client and run `npm install`

Go to /site/templates/client/config/index.js and change the target in *proxyTable* to match your URL:

```
proxyTable: {
  '/api': {
    target: 'http://change-to-your-dev-host.dev',
    changeOrigin: true
  }
},
```

Now run `npm run dev`, point your browser to http://localhost:8080 and you should be able to perform a login with your user.

Check the files components/Login.vue, components/Content.vue and the main.js inside /site/templates/client to learn how the login process works.

As a last step you should change your JWT Secret in your config.php. You can basically use any string but a good idea is to create a random string with the following PHP command:

`echo base64_encode(openssl_random_pseudo_bytes(64));`

### Helper

There is a small helper class, which exposes some often used functionality. At the moment there's basically just one function available, but I for my part use it all the time: `checkAndSanitizeRequiredParameters`. This function checks if the client send all the parameters required and sanitizes them against a specified ProcessWire sanitizer. To use it call it first thing in your Api endpoint function:
```
public static function postWithSomeData($data) {
  // Check for required parameter "message" and sanitize with PW Sanitizer
  $data = ApiHelper::checkAndSanitizeRequiredParameters($data, ['message|text']);

  return "Your message is: " . $data->message;
}
```

An example can be found here: https://github.com/thomasaull/RestApiProfile/blob/master/templates/api/Test.php#L15
