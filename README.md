# Phantasos Client Bundle
The purpose of this Symfony bundle is to provide a set of tools to make connecting to our simple media server, [Phantasos](https://github.com/MESD/Phantasos)

## Getting Started
To install the bundle with your Symfony application, use composer to add the client to your application.
```bash
$ composer require mesd/phantasos-client-bundle "~0.1"
```
Then add the bundle to your app kernel.
```php
$bundles = array(
  ...
  new Mesd\PhantasosClientBundle\MesdPhantasosClientBundle()
);
```
After that, the settings for the client need to be configured in your config.yml file.
```yaml
mesd_phantasos_client:
  host: %phantasos_host%
  apikey: %phantasos_apikey%
  ready_callback_route: my_app_media_callback
```
The host is where the root of the Phantasos's rest api is, and apikey is the generated apikey for this application from Phantasos.  The ready callback route is optional and takes the name of one of your application's named symfony routes to callback when the media done processing and is ready for playback.  An example of what this action will look like is below.  The method for the action needs to be POST, and the media id for the media that the call is being made about is in the body of the request under the name of 'media_id'.
```php
/**
 * @Route('/myapp/media_is_ready', name="my_app_media_callback")
 * @Method({"POST"})
 */
public function myMediaIsDoneCallbackAction(Request $request)
{
  $mediaId = $request->request->get('media_id');

  // Application specific stuff here ...
  
  return new Response('Message Recieved', 200);
}
```
If everything went as expected, then your application is ready to use the client.

##Usage
