<?php

namespace Mesd\PhantasosClientBundle\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\JsonResponse;

use Mesd\PhantasosClientBundle\Model\MediaTypes;

class ClientController extends Controller
{
    /**
     * @Route("/phantasos/mediainfo/{mediaId}", name="MesdPhantasosClient_getInfo")
     * @Method({"GET"})
     *
     * This action returns the media info in a json format. It serves as a pass
     * thru to keep the api key secure and unknown to the end client
     */
    public function getMediaInfoAction(Request $request, $mediaId)
    {
        return new JsonResponse(
            $this->get('mesd_phantasos_client')->getRawMediaInfo($mediaId)
        );
    }
    
    /**
     * @Route("/phantasos/medialinks/{mediaId}", name="MesdPhantasosClient_getLinks")
     * @Method({"GET"})
     */
    public function getMediaLinksAction(Request $request, $mediaId)
    {
        // Get the info for the file
        $info = $this->get('mesd_phantasos_client')->getMediaInfo($mediaId);

        // If the info is nonexistent throw a 404
        if (null === $info) {
            return new Response('Media does not exist', 404);
        }

        // Get the files and make the links for them
        return $this->render(
            'MesdPhantasosClientBundle::links.html.twig',
            array('info' => $info)
        );
    }

    /**
     * @Route("/phantasos/mediafile/{mediaFileId}/{fileName}", name="MesdPhantasosClient_getMedia")
     * @Method({"GET"})
     *
     * This action routes media files from Phantasos to the client app
     */
    public function getMediaAction(Request $request, $mediaFileId, $fileName)
    {
        // Get the range request if exists
        if ($request->headers->has('Range')) {
            $range = $request->headers->get('Range');
        } else {
            $range = null;
        }

        // Get the media stream
        $media = $this->get('mesd_phantasos_client')->getMediaFile(
            $mediaFileId,
            $range
        );

        $headers = array(
            'Content-Length' => $media->getHeader('Content-Length'),
            'Content-Type' => $media->getHeader('Content-Type'),
            'Accept-Ranges' => $media->getHeader('Accept-Ranges'),
            'Cache-Control' => $media->getHeader('Cache-Control')
        );

        // Check if a partial encoding exists
        if ($media->hasHeader('Content-Range')) {
            $status = 206;
            $headers['Content-Range'] = $media->getHeader('Content-Range');
        } else {
            $status = 200;
        }

        // Return the response
        return new Response($media->getBody(), $status, $headers);
    }

    /**
     * @Route("/phantasos/embed/{mediaId}/{width}/{height}", name="MesdPhantasosClient_embedMedia", defaults={"width" = 1280, "height" = 720})
     * @Method({"GET"})
     *
     * This action renders a viewer for a particular piece of media
     */
    public function embedMediaAction(Request $request, $mediaId, $width, $height)
    {
        // Get the info for the file
        $info = $this->get('mesd_phantasos_client')->getMediaInfo($mediaId);

        // If the info is nonexistent throw a 404
        if (null === $info) {
            return new Response('Media does not exist', 404);
        }

        // Render the waiting screen if the media is not yet ready
        if (!$info->getReady()) {
            return $this->render(
                'MesdPhantasosClientBundle::not_ready.html.twig',
                array('info' => $info, 'mediaId' => $mediaId, 'width' => $width, 'height' => $height)
            );
        }

        // Determine which twig to render based on the media type
        switch ($info->getType()) {
            case MediaTypes::VIDEO:
                return $this->render(
                    'MesdPhantasosClientBundle::video.html.twig',
                    array('info' => $info, 'width' => $width, 'height' => $height)
                );
                break;
            case MediaTypes::AUDIO:
                return $this->render(
                    'MesdPhantasosClientBundle::audio.html.twig',
                    array('info' => $info, 'width' => $width, 'height' => $height)
                );
                break;
            case MediaTypes::IMAGE:
                return $this->render(
                    'MesdPhantasosClientBundle::image.html.twig',
                    array('info' => $info, 'width' => $width, 'height' => $height)
                );
                break;
            case MediaTypes::PDF:
                return $this->render(
                    'MesdPhantasosClientBundle::pdf.html.twig',
                    array('info' => $info, 'width' => $width, 'height' => $height)
                );
                break;
            case MediaTypes::UNKNOWN:
            default:
                return $this->render(
                    'MesdPhantasosClientBundle::unknown.html.twig',
                    array('info' => $info, 'width' => $width, 'height' => $height)
                );
        }
    }
}
