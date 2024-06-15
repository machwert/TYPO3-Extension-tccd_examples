<?php

declare(strict_types=1);

namespace Machwert\TccdExamples\Domain\Model;

use TYPO3\CMS\Extbase\Domain\Model\Category;
use TYPO3\CMS\Extbase\Domain\Model\FileReference;
use TYPO3\CMS\Extbase\Persistence\ObjectStorage;
use TYPO3\CMS\Extbase\Annotation\ORM\Lazy;

/**
 * This file is part of the "TCCD examples" Extension for TYPO3 CMS.
 *
 * For the full copyright and license information, please read the
 * LICENSE.txt file that was distributed with this source code.
 *
 * (c) 2024 Volker Golbig <typo3@machwert.de>, machwert
 */

/**
 * Tccd
 */
class Tccd extends \TYPO3\CMS\Extbase\DomainObject\AbstractEntity
{

    /**
     * Title
     *
     * @var string
     * @TYPO3\CMS\Extbase\Annotation\Validate("NotEmpty")
     */
    protected $title = null;

    /**
     * Description
     *
     * @var string
     */
    protected $description = null;

    /**
     * Syllabusdescription
     *
     * @var string
     */
    protected $syllabusdescription = null;

    /**
     * Version
     *
     * @var int
     * @TYPO3\CMS\Extbase\Annotation\Validate("NotEmpty")
     */
    protected $version = null;

    /**
     * Link
     *
     * @var string
     */
    protected $link = null;

    /**
     * Slug
     *
     * @var string
     * @TYPO3\CMS\Extbase\Annotation\Validate("NotEmpty")
     */
    protected $slug = null;

    /**
     * edited
     *
     * @var bool
     */
    protected $edited = null;

    // [4] - File abstraction layer (FAL)
    /**
     * @var ObjectStorage<FileReference>
     * @Lazy
     */
    protected ?ObjectStorage $images = null;

    /**
     * @var ObjectStorage<Category>
     * @Lazy
     */
    protected ObjectStorage $categories;

    /**
     * Version
     *
     * @var int
     * @TYPO3\CMS\Extbase\Annotation\Validate("NotEmpty")
     */
    protected ?int $sysLanguageUid = 0;

    /**
     * Version
     *
     * @var int
     * @TYPO3\CMS\Extbase\Annotation\Validate("NotEmpty")
     */
    protected ?int $l10nParent = 0;


    public function __construct()
    {
        $this->setCategories(new \TYPO3\CMS\Extbase\Persistence\ObjectStorage);
    }

    public function initializeObject(): void
    {
        $this->categories = new ObjectStorage();
        $this->images = new ObjectStorage();
    }

    /**
     * Returns the title
     *
     * @return string
     */
    public function getTitle()
    {
        return $this->title;
    }

    /**
     * Sets the title
     *
     * @param string $title
     * @return void
     */
    public function setTitle(string $title)
    {
        $this->title = $title;
    }

    /**
     * Returns the description
     *
     * @return string
     */
    public function getDescription()
    {
        return $this->description;
    }

    /**
     * Sets the description
     *
     * @param string $description
     * @return void
     */
    public function setDescription(string $description)
    {
        $this->description = $description;
    }

    /**
     * Returns the syllabusdescription
     *
     * @return string
     */
    public function getSyllabusdescription()
    {
        return $this->syllabusdescription;
    }

    /**
     * Sets the syllabusdescription
     *
     * @param string $syllabusdescription
     * @return void
     */
    public function setSyllabusdescription(?string $syllabusdescription)
    {
        $this->syllabusdescription = $syllabusdescription;
    }

    /**
     * Returns the version
     *
     * @return int
     */
    public function getVersion()
    {
        return $this->version;
    }

    /**
     * Sets the version
     *
     * @param int $version
     * @return void
     */
    public function setVersion(int $version)
    {
        $this->version = $version;
    }


    /**
     * Returns the slug
     *
     * @return string
     */
    public function getSlug()
    {
        return $this->slug;
    }

    /**
     * Sets the slug
     *
     * @param string $slug
     * @return void
     */
    public function setSlug(string $slug)
    {
        $this->slug = $slug;
    }

    /**
     * Returns the edited
     *
     * @return int
     */
    public function getEdited()
    {
        return $this->edited;
    }

    /**
     * Sets the edited
     *
     * @param int $edited
     * @return void
     */
    public function setEdited(bool $edited)
    {
        $this->edited = $edited;
    }

    // [4] - File abstraction layer (FAL)

    public function addImages(FileReference $images): void
    {
        $this->images->attach($images);
    }

    public function removeImages(FileReference $imagesToRemove): void
    {
        $this->images->detach($imagesToRemove);
    }

    /**
     * Gets the images
     *
     * @return ObjectStorage $images
     */
    public function getImages(): ?ObjectStorage
    {
        return $this->images;
    }

    public function setImages(?ObjectStorage $images): void
    {
        $this->images = $images;
    }

    public function hasImages(): bool
    {
        if ($this->images) {
            return true;
        } else {
            return false;
        }
    }

    public function isImages(?ObjectStorage $images): bool
    {
        if ($this->images) {
                return true;
        } else {
            return false;
        }
    }

    /**
     * Add category to a blog
     *
     * @param Category $category
     */
    public function addCategory(Category $category)
    {
        $this->categories->attach($category);
    }

    /**
     * Set categories
     */
    public function setCategories(ObjectStorage $categories)
    {
        $this->categories = $categories;
    }

    /**
     * Get categories
     */
    public function getCategories(): ObjectStorage
    {
        return $this->categories;
    }

    /**
     * Remove category from blog
     */
    public function removeCategory(Category $category)
    {
        $this->categories->detach($category);
    }

    /**
     * Returns the sysLanguageUid
     *
     * @return int
     */
    public function getSysLanguageUid()
    {
        return $this->sysLanguageUid;
    }

    /**
     * Sets the sysLanguageUid
     *
     * @param int $sysLanguageUid
     * @return void
     */
    public function setSysLanguageUid(int $sysLanguageUid)
    {
        $this->sysLanguageUid = $sysLanguageUid;
    }

    /**
     * Returns the l10nParent
     *
     * @return int
     */
    public function getL10nParent()
    {
        return $this->l10nParent;
    }

    /**
     * Sets the l10nParent
     *
     * @param int $l10nParent
     * @return void
     */
    public function setL10nParent(int $l10nParent)
    {
        $this->l10nParent = $l10nParent;
    }

    public function getAllProperties() {
        return get_object_vars($this);
    }
}
