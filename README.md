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

The Rest-API should work now. To check you can use [Postman](https://www.getpostman.com/) or [Insomnia](https://insomnia.rest/) and run a GET Request:

`http://pwrestapi.dev/api/test`

You should get the following error:

```
{
	"error": "No Authorization Header found"
}
```

Because you’re not authenticated yet. To disable authentication, go to /site/templates/api/Router.php and in the function *handle* set the variable $authActive to false.

If you run the same Request again, you’ll get the following:
```
{
	"user": "guest"
}
```

To use JWT-Auth you have to send a GET Request to /auth with two parameters, username and password. The API will log your user in and return you the JWT-Token, which you have to add to every following request.

An example for a simple login form is implemented as a Vue SPA.
To install, go to /site/templates/client and run the following commands

```
npm install
npm run dev
```

Go to /site/templates/client/config/index.js and change the target in *proxyTable* to match your URL:

```
proxyTable: {
	'/api': {
		target: 'http://pwrestapi.dev',
		changeOrigin: true
	}
},
```

Point your browser to http://localhost:8080 and you should be able to login with your user.

Check the files components/Login.vue, components/Content.vue and the main.js inside /site/templates/client to learn how the login  process works.

As a last step you should change your JWT Secret in your config.php. You can basically use any string but a good idea is to create a random string with the following PHP command:

`echo base64_encode(openssl_random_pseudo_bytes(64));`
