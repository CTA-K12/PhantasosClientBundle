<?php

namespace Mesd\PhantasosClientBundle\Model;

/**
 * Simple container for media info
 */
class MediaInfo
{
    ////////////////
    // PROPERTIES //
    ////////////////

    /**
     * Owning client
     * @var string
     */
    private $owner;

    /**
     * Media type
     * @var string
     */
    private $type;

    /**
     * Whether the original file still exists
     * @var boolean
     */
    private $originalExists;

    /**
     * Tags
     * @var array
     */
    private $tags;

    /**
     * Security tags
     * @var array
     */
    private $securityTags;

    /**
     * Array of file info
     * @var array
     */
    private $files;

    /**
     * Table for helping with getting the appropiate size
     * @var array
     */
    private $formatSizeGrid;

    /**
     * Pointer to the original
     * @var MediaFileInfo
     */
    private $original;

    /**
     * Whether the media is ready to view
     * @var boolean
     */
    private $ready;

    /**
     * Status of the media
     * @var string
     */
    private $status;

    /**
     * Percentage complete when the status is processing
     * @var float
     */
    private $processingPercentage;

    /////////////////
    // CONSTRUCTOR //
    /////////////////


    public function __construct()
    {
        // Init
        $this->tags = array();
        $this->securityTags = array();
        $this->files = array();
        $this->formatSizeGrid = array();
    }


    ////////////////////
    // STATIC METHODS //
    ////////////////////

    /**
     * Create a new media info object from array data
     * @param array $data Data array
     */
    public static function createFromArray(array $data)
    {
        $info = new static();
        $info->setOwner($data['owner']);
        $info->setType($data['type']);
        $info->setOriginalExists($data['original_exists'] == 1 ? true : false);
        $info->setReady($data['ready'] == 1 ? true : false);
        $info->setStatus($data['status']);
        $info->setProcessingPercentage($data['processing_percentage']);
        $info->setTags($data['tags']);
        $info->setSecurityTags($data['security']);

        foreach($data['files'] as $fileData) {
            $fileInfo = new MediaFileInfo();
            $fileInfo->setName($fileData['file_name']);
            $fileInfo->setContentType($fileData['content_type']);
            $fileInfo->setMediaFIleId($fileData['media_file_id']);
            if (isset($fileData['size']['width'])) {
                $fileInfo->setWidth($fileData['size']['width']);
            }
            if (isset($fileData['size']['height'])) {
                $fileInfo->setHeight($fileData['size']['height']);
            }
            if (isset($fileData['size']['bitrate'])) {
                $fileInfo->setBitrate($fileData['size']['bitrate']);
            }
            $info->addFileInfo($fileInfo);
        }

        return $info;
    }

    /////////////
    // METHODS //
    /////////////

    /**
     * Get media file id of smallest version of particular format
     * @param string $format Format type
     * @return string Media file id of the smallest of the given format
     */
    public function getSmallestOfFormat($format)
    {
        if (array_key_exists($format, $this->formatSizeGrid)) {
            return $this->formatSizeGrid[$format]->bottom();
        } else {
            return null;
        }
    }

    /**
     * Get media file id of largest version of particular format
     * @param string $format Format type
     * @return string Media file id of the largest of the given format
     */
    public function getLargestOfFormat($format)
    {
        if (array_key_exists($format, $this->formatSizeGrid)) {
            return $this->formatSizeGrid[$format]->top();
        } else {
            return null;
        }
    }

    /**
     * Get the media file id for a file of the given format closest in size
     * @param string $format  Format to get
     * @param int    $width   Width
     * @param int    $height  Height
     * @param int    $bitrate Bitrate in kbps
     * @return string Media file id of the requested
     */
    public function getSizeForFormat(
        $format,
        $width = null,
        $height = null,
        $bitrate = null)
    {
        if (array_key_exists($format, $this->formatSizeGrid)) {
            $list = $this->formatSizeGrid[$format];
            $list->rewind();
            while($list->valid()) {
                if ($list->current()->compareSize($width, $height, $bitrate) > -1) {
                    // @TODO this is kinda ulgy right now, need to fixup
                    return $list->current();
                }
                $list->next();
            }
            return $list->top();
        } else {
            return null;
        }
    }

    /**
     * Add file info
     * @param MediaFileInfo $fileInfo file info to addd
     * @return self
     */
    public function addFileInfo(MediaFileInfo $fileInfo)
    {
        // Add the default list
        $this->files[] = $fileInfo;

        // Check if this is the original
        if (1 === preg_match('/^original/', $fileInfo->getName())) {
            $this->original = $fileInfo;
        } else {
            // Place into the format size grid
            if (!array_key_exists(
                    $fileInfo->getContentType(),
                    $this->formatSizeGrid)
            ) {
                $this->formatSizeGrid[$fileInfo->getContentType()] =
                    new \SplDoublyLinkedList();
            }

            // Go through the list to determine where the new file info should go
            // list should be organized by smallest first
            $list = $this->formatSizeGrid[$fileInfo->getContentType()];
            if ($list->isEmpty()) {
                $list->push($fileInfo);
            } else {
                $inserted = false;
                $list->rewind();
                while($list->valid()) {
                    // Compare
                    if ($list->current()->compareSizeToOther($fileInfo) > 0) {
                        $list->add($list->key(), $fileInfo);
                        $inserted = true;
                        break;
                    }

                    $list->next();
                }
                if (!$inserted) {
                    $list->push($fileInfo);
                }
            }
        }

        // Return
        return $this;
    }

    /**
     * Returns media files in an array keyed by width, and any item without width
     * will be excluded
     * @return array
     */
    public function getFilesByWidth()
    {
        $ret = array();
        foreach($this->files as $file) {
            if (null !== $file->getWidth()) {
                $ret[$file->getWidth()] = $file;
            }
        }
        return $ret;
    }

    /////////////////////////
    // GETTERS AND SETTERS //
    /////////////////////////

    /**
     * Get the value of Owning client
     * @return string
     */
    public function getOwner()
    {
        return $this->owner;
    }

    /**
     * Set the value of Owning client
     * @param string owner
     * @return self
     */
    public function setOwner($owner)
    {
        $this->owner = $owner;
        return $this;
    }

    /**
     * Get the value of Media type
     * @return string
     */
    public function getType()
    {
        return $this->type;
    }

    /**
     * Set the value of Media type
     * @param string type
     * @return self
     */
    public function setType($type)
    {
        $this->type = $type;
        return $this;
    }

    /**
     * Get the value of Whether the original file still exists
     * @return boolean
     */
    public function getOriginalExists()
    {
        return $this->originalExists;
    }

    /**
     * Set the value of Whether the original file still exists
     * @param boolean originalExists
     * @return self
     */
    public function setOriginalExists($originalExists)
    {
        $this->originalExists = $originalExists;
        return $this;
    }

    /**
     * Get the value of Tags
     * @return array
     */
    public function getTags()
    {
        return $this->tags;
    }

    /**
     * Set the value of Tags
     * @param array tags
     * @return self
     */
    public function setTags(array $tags)
    {
        $this->tags = $tags;
        return $this;
    }

    /**
     * Get the value of Security tags
     * @return array
     */
    public function getSecurityTags()
    {
        return $this->securityTags;
    }

    /**
     * Set the value of Security tags
     * @param array securityTags
     * @return self
     */
    public function setSecurityTags(array $securityTags)
    {
        $this->securityTags = $securityTags;
        return $this;
    }

    /**
     * Get the value of Array of file info
     * @return array
     */
    public function getFileInfo()
    {
        return $this->files;
    }

    /**
     * Get the original
     * @return MediaFileInfo
     */
    public function getOriginal()
    {
        return $this->original;
    }

    /**
     * Get the value of Whether the media is ready to view
     * @return boolean
     */
    public function getReady()
    {
        return $this->ready;
    }

    /**
     * Set the value of Whether the media is ready to view
     * @param boolean ready
     * @return self
     */
    public function setReady($ready)
    {
        $this->ready = $ready;
        return $this;
    }

    /**
     * Get the value of Status of the media
     * @return string
     */
    public function getStatus()
    {
        return $this->status;
    }

    /**
     * Set the value of Status of the media
     * @param string status
     * @return self
     */
    public function setStatus($status)
    {
        $this->status = $status;
        return $this;
    }

    /**
     * Get the value of Percentage complete when the status is processing
     * @return float
     */
    public function getProcessingPercentage()
    {
        return $this->processingPercentage;
    }

    /**
     * Set the value of Percentage complete when the status is processing
     * @param float processingPercentage
     * @return self
     */
    public function setProcessingPercentage($processingPercentage)
    {
        $this->processingPercentage = $processingPercentage;
        return $this;
    }
}
