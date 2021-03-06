<?php

/**
 * This file is part of the <name> project.
 *
 * (c) <yourname> <youremail>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Application\Sonata\MediaBundle\Entity;
use Gedmo\Translatable\Entity\MappedSuperclass\AbstractTranslation;

/**
 * This file has been generated by the Sonata EasyExtends bundle ( http://sonata-project.org/bundles/easy-extends )
 *
 * References :
 *   working with object : http://www.doctrine-project.org/projects/orm/2.0/docs/reference/working-with-objects/en
 *
 * @author <yourname> <youremail>
 */
class GalleryTranslation extends AbstractTranslation
{
    
    protected $id;

    protected $object;

    protected $locale;

    /**
     * @var string
     *
     */
    protected $title;

    protected $customDescription;

    /**
     * Convinient constructor
     *
     * @param string $title
     */
    public function __construct($title = null)
    {
        $this->title = $title;
    }


    /**
     * Get title
     *
     * @return string $title
     */
    public function getTitle() {
        return $this->title;
    }
    
    /**
     * Set title
     *
     * @param string $title
     */
    public function setTitle($title) {
        $this->title = $title;
    
        return $this;
    }

    /**
     * Gets the value of locale.
     *
     * @return mixed
     */
    public function getLocale()
    {
        return $this->locale;
    }

    /**
     * Sets the value of locale.
     *
     * @param mixed $locale the locale
     *
     * @return self
     */
    public function setLocale($locale)
    {
        $this->locale = $locale;

        return $this;
    }

    /**
     * Gets the value of customDescription.
     *
     * @return mixed
     */
    public function getCustomDescription()
    {
        return $this->customDescription;
    }

    /**
     * Sets the value of customDescription.
     *
     * @param mixed $customDescription the custom description
     *
     * @return self
     */
    public function setCustomDescription($customDescription)
    {
        $this->customDescription = $customDescription;

        return $this;
    }
}