<?php

namespace Mesd\PhantasosClientBundle\Model;

class UploadTicket
{
    ////////////////
    // PROPERTIES //
    ////////////////

    /**
     * Media ID
     * @var string
     */
    private $mediaId;

    /**
     * Route for the end client to upload to
     * @var string
     */
    private $uploadRoute;

    /**
     * Expiration time for the upload
     * @var \DateTIme
     */
    private $expiration;

    /////////////////////////
    // GETTERS AND SETTERS //
    /////////////////////////

    /**
     * Get the value of Media ID
     * @return string
     */
    public function getMediaId()
    {
        return $this->mediaId;
    }

    /**
     * Set the value of Media ID
     * @param string mediaId
     * @return self
     */
    public function setMediaId($mediaId)
    {
        $this->mediaId = $mediaId;
        return $this;
    }

    /**
     * Get the value of Route for the end client to upload to
     * @return string
     */
    public function getUploadRoute()
    {
        return $this->uploadRoute;
    }

    /**
     * Set the value of Route for the end client to upload to
     * @param string uploadRoute
     * @return self
     */
    public function setUploadRoute($uploadRoute)
    {
        $this->uploadRoute = $uploadRoute;
        return $this;
    }

    /**
     * Get the value of Expiration time for the upload
     * @return \DateTIme
     */
    public function getExpiration()
    {
        return $this->expiration;
    }

    /**
     * Set the value of Expiration time for the upload
     * @param \DateTIme expiration
     * @return self
     */
    public function setExpiration(\DateTIme $expiration)
    {
        $this->expiration = $expiration;
        return $this;
    }
}
