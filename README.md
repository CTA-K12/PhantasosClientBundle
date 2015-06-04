# Phantasos Client Bundle
The purpose of this Symfony bundle is to provide a set of tools to make connecting to our simple media server, [Phantasos](https://github.com/MESD/Phantasos).

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
The client bundle will add a new service to the dependency injection container called 'mesd_phantasos_client' that will return the Phantasos Client preconstructed with the host and apikey information needed to make calls to the media server.

###Uploading
Uploading to Phantasos is a two step process.  First an upload ticket needs to be retrieved from Phantasos along with an upload route.  Then the end user will directly upload the media to media server.  To obtain an the upload ticket information from the client, do as follows.
```php
$ticket = $this->get('mesd_phantasos_client')->requestUploadTicket();

// The upload ticket contains information such as the media id that will need to be saved in your application
$mediaLink = new MediaLink() // Or whatever you are using to persist the media information in the using application
$mediaLink->setMediaId($ticket->getMediaId());
// Persist the media link and do whatever other application specific things need to be done
```
After obtaining the ticket and persisting the information you want to save, provide the user with the a simple upload form in html that has the action pointing to the url provided by in the upload ticket.  Note, that the media server will return a set of responses back when uploading, so you may want to use some javascript on the end user side to track these in case an error is thrown or to mark that the media was successfully uploaded on the using application side.

###Playback
The easist way to have playback for the media is to embed the following twig directive in your template where you wish to display the media, where 'myMediaId', is the media id of the media you wish to display and width and height are the resolution in pixels you wish to display the media as.
```twig
{{ render(url('MesdPhantasosClient_embedMedia', { 'mediaId': myMediaId, 'width': 800, 'height': 600 })) }}
```
The bundle can support PDFs, video, audio, and images, all the types of media currently supported by Phantasos.

Given that the support for streaming directly from the end user form the server is not currently finished or in place, the client my change later in regards to playback.
