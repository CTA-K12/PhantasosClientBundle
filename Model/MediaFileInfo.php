<?php

namespace Mesd\PhantasosClientBundle\Model;

/**
 * Simple container for file info
 */
class MediaFileInfo
{
    ////////////////
    // PROPERTIES //
    ////////////////

    /**
     * Filename
     * @var string
     */
    private $name;

    /**
     * Content Type
     * @var string
     */
    private $contentType;

    /**
     * Media file id
     * @var string
     */
    private $mediaFileId;

    /**
     * Width if applicable
     * @var int
     */
    private $width;

    /**
     * Height if applicable
     * @var int
     */
    private $height;

    /**
     * Bitrate in kbps
     * @var int
     */
    private $bitrate;

    /////////////////
    // CONSTRUCTOR //
    /////////////////

    /**
     * Constructor
     */
    public function __construct()
    {
        // Nothing for now
    }

    /////////////
    // METHODS //
    /////////////

    /**
     * Compare this to the another media file info on basis of size
     * @param  MediaFileInfo $b Other
     * @return int              -1 if this is less, 0 if equal, 1 if this is greater
     */
    public function compareSizeToOther(MediaFileInfo $b)
    {
        // Check bitrate first
        if (null !== $this->getBitrate() && null !== $b->getBitrate()) {
            if ($this->getBitrate() < $b->getBitrate()) {
                return -1;
            } elseif ($this->getBitrate() > $b->getBitrate()) {
                return 1;
            }
        }

        // Check width second
        if (null !== $this->getWidth() && null !== $b->getWidth()) {
            if ($this->getWidth() < $b->getWidth()) {
                return -1;
            } elseif ($this->getWidth() > $b->getWidth()) {
                return 1;
            }
        }

        // Check height last
        if (null !== $this->getHeight() && null !== $b->getHeight()) {
            if ($this->getHeight() < $b->getHeight()) {
                return -1;
            } elseif ($this->getHeight() > $b->getHeight()) {
                return 1;
            }
        }

        // If still here return 0
        return 0;
    }

    /**
     * Compare this to the another media file info on basis of size
     * @param  int $width
     * @param  int $height
     * @param  int $bitrate In kbps
     * @return int -1 if this is less, 0 if equal, 1 if this is greater
     */
    public function compareSize($width = null, $height = null, $bitrate = null)
    {
        // Check bitrate first
        if (null !== $this->getBitrate() && null !== $bitrate) {
            if ($this->getBitrate() < $bitrate) {
                return -1;
            } elseif ($this->getBitrate() > $bitrate) {
                return 1;
            }
        }

        // Check width second
        if (null !== $this->getWidth() && null !== $width) {
            if ($this->getWidth() < $width) {
                return -1;
            } elseif ($this->getWidth() > $width) {
                return 1;
            }
        }

        // Check height last
        if (null !== $this->getHeight() && null !== $height) {
            if ($this->getHeight() < $height) {
                return -1;
            } elseif ($this->getHeight() > $height) {
                return 1;
            }
        }

        // If still here return 0
        return 0;
    }

    /////////////////////////
    // GETTERS AND SETTERS //
    /////////////////////////

    /**
     * Get the value of Filename
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * Set the value of Filename
     * @param string name
     * @return self
     */
    public function setName($name)
    {
        $this->name = $name;
        return $this;
    }

    /**
     * Get the value of Content Type
     * @return string
     */
    public function getContentType()
    {
        return $this->contentType;
    }

    /**
     * Set the value of Content Type
     * @param string contentType
     * @return self
     */
    public function setContentType($contentType)
    {
        $this->contentType = $contentType;
        return $this;
    }

    /**
     * Get the value of Media file id
     * @return string
     */
    public function getMediaFileId()
    {
        return $this->mediaFileId;
    }

    /**
     * Set the value of Media file id
     * @param string mediaFileId
     * @return self
     */
    public function setMediaFileId($mediaFileId)
    {
        $this->mediaFileId = $mediaFileId;
        return $this;
    }

    /**
     * Get the value of Width if applicable
     * @return int
     */
    public function getWidth()
    {
        return $this->width;
    }

    /**
     * Set the value of Width if applicable
     * @param int width
     * @return self
     */
    public function setWidth($width)
    {
        $this->width = $width;
        return $this;
    }

    /**
     * Get the value of Height if applicable
     * @return int
     */
    public function getHeight()
    {
        return $this->height;
    }

    /**
     * Set the value of Height if applicable
     * @param int height
     * @return self
     */
    public function setHeight($height)
    {
        $this->height = $height;
        return $this;
    }

    /**
     * Get the value of Bitrate in kbps
     * @return int
     */
    public function getBitrate()
    {
        return $this->bitrate;
    }

    /**
     * Set the value of Bitrate in kbps
     * @param int bitrate
     * @return self
     */
    public function setBitrate($bitrate)
    {
        $this->bitrate = $bitrate;
        return $this;
    }
}
