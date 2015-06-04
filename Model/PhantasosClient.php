<?php

namespace Mesd\PhantasosClientBundle\Model;

use GuzzleHttp\Client;
use Symfony\Bundle\FrameworkBundle\Routing\Router;

use Mesd\PhantasosClientBundle\Model\UploadTicket;

class PhantasosClient
{
    ////////////////
    // PROPERTIES //
    ////////////////

    /**
     * Root url to the phantasos instance
     * @var string
     */
    protected $host;

    /**
     * Apikey
     * @var string
     */
    protected $apikey;

    /**
     * Optional route to callback when the media is ready
     * @var string
     */
    protected $readyCallbackRoute;

    /**
     * Router
     * @var Router
     */
    protected $router;

    /////////////////
    // CONSTRUCTOR //
    /////////////////


    public function __construct(Router $router)
    {
        // Set
        $this->router = $router;

        // Init
        $this->readyCallbackRoute = null;
    }

    /////////////
    // METHODS //
    /////////////

    /**
     * Recieve an upload ticket
     * @param array   $tags         Tags for the upload
     * @param array   $securityTags Security Tags for the upload
     * @param boolean $hideToOthers Whether to make this application the sole viewer
     */
    public function requestUploadTicket(
        $tags = array(),
        $securityTags = array(),
        $hideToOthers = true)
    {
        // Create the data array
        $data = array(
            'apikey' => $this->apikey,
            'tags' => $tags,
            'security' => $securityTags,
            'hide_to_others' => $hideToOthers
        );

        // Generate a new Post request
        $client = new Client();

        // Create a callback route if set
        if (null !== $this->readyCallbackRoute) {
            $data['callback_route'] =
                $this->router->getContext()->getHost() .
                $this->router->generate($this->readyCallbackRoute)
            ;
        }

        // Send the request
        $response = $client->post($this->host . '/api/requestUploadTicket', array(
            'body' => $data
        ));

        // If we got a 200 create + send back the upload ticket
        if (200 == $response->getStatusCode()) {
            $uploadTicket = new UploadTicket();
            $responseArray = $response->json();
            $uploadTicket->setUploadRoute($responseArray['upload_route']);
            $uploadTicket->setMediaId($responseArray['media_id']);
            $uploadTicket->setExpiration(
                new \DateTime($responseArray['expiration'])
            );

            return $uploadTicket;
        } else {
            throw new \Exception(
                $response->getStatusCode() . ': ' . $response->getReasonPhrase()
            );
        }
    }

    /**
     * Get info for the requested media id
     * @param string $mediaId Id of the media
     * @return MediaInfo Media info object
     */
    public function getMediaInfo($mediaId)
    {
        return MediaInfo::createFromArray($this->getRawMediaInfo($mediaId));
    }

    /**
     * Get info for the requested media id
     * @param string $mediaId Id of the media
     * @return array Media info
     */
    public function getRawMediaInfo($mediaId)
    {
        // Create the data array
        $data = array(
            'apikey' => $this->apikey,
            'media_id' => $mediaId
        );

        // Generate a new Post request
        $client = new Client();
        $response = $client->get($this->host . '/api/mediaInfo', array(
            'query' => $data
        ));

        // Read out the response
        return $response->json();
    }

    /**
     * Get media
     * @param string $mediaFileId Id of the media file to retrieve
     * @param string $range       Range request if exists
     * @return Response Guzzle Stream Response
     */
    public function getMediaFile($mediaFileId, $range = null)
    {
        // Create the data array
        $data = array(
            'apikey' => $this->apikey,
            'media_file_id' => $mediaFileId
        );

        // Create the headers array
        if (null === $range) {
            $headers = array();
        } else {
            $headers = array(
                'Range' => $range
            );
        }

        // Generate a new Post request
        $client = new Client();

        // Send the request
        $response = $client->get($this->host . '/api/requestMedia', array(
            'query' => $data,
            'headers' => $headers
        ));

        // Return the raw media
        return $response;
    }

    /////////////
    // SETTERS //
    /////////////

    /**
     * Set the value of Root url to the phantasos instance
     * @param string host
     * @return self
     */
    public function setHost($host)
    {
        $this->host = $host;
        return $this;
    }

    /**
     * Set the value of Apikey
     * @param string apikey
     * @return self
     */
    public function setApikey($apikey)
    {
        $this->apikey = $apikey;
        return $this;
    }

    /**
     * Set the value of Optional route to callback when the media is ready
     * @param string readyCallbackRoute
     * @return self
     */
    public function setReadyCallback($readyCallbackRoute)
    {
        $this->readyCallbackRoute = $readyCallbackRoute;
        return $this;
    }
}
