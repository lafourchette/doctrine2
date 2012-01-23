<?php

namespace Doctrine\Tests\Models\CMS;

use Doctrine\Common\Collections\ArrayCollection;

/**
 * @Entity
 * @Table(name="cms_users")
 * @NamedQueries({
 *     @NamedQuery(name="all", query="SELECT u FROM __CLASS__ u")
 * })
 */
class CmsUser
{
    /**
     * @Id @Column(type="integer")
     * @GeneratedValue
     */
    public $id;
    /**
     * @Column(type="string", length=50, nullable=true)
     */
    public $status;
    /**
     * @Column(type="string", length=255, unique=true)
     */
    public $username;
    /**
     * @Column(type="string", length=255)
     */
    public $name;
    /**
     * @EmbedOne(class="CmsParents", prefix="parents")
     */
    public $parents;
    /**
     * @OneToMany(targetEntity="CmsPhonenumber", mappedBy="user", cascade={"persist", "merge"}, orphanRemoval=true)
     */
    public $phonenumbers;
    /**
     * @OneToMany(targetEntity="CmsArticle", mappedBy="user", cascade={"detach"})
     */
    public $articles;
    /**
     * @OneToOne(targetEntity="CmsAddress", mappedBy="user", cascade={"persist"}, orphanRemoval=true)
     */
    public $address;
    /**
     * @OneToOne(targetEntity="CmsEmail", inversedBy="user", cascade={"persist"}, orphanRemoval=true)
     * @JoinColumn(referencedColumnName="id", nullable=true)
     */
    public $email;
    /**
     * @ManyToMany(targetEntity="CmsGroup", inversedBy="users", cascade={"persist", "merge", "detach"})
     * @JoinTable(name="cms_users_groups",
     *      joinColumns={@JoinColumn(name="user_id", referencedColumnName="id")},
     *      inverseJoinColumns={@JoinColumn(name="group_id", referencedColumnName="id")}
     *      )
     */
    public $groups;

    public function __construct() {
        $this->phonenumbers = new ArrayCollection;
        $this->articles = new ArrayCollection;
        $this->groups = new ArrayCollection;
    }

    public function getId() {
        return $this->id;
    }

    public function getStatus() {
        return $this->status;
    }

    public function getUsername() {
        return $this->username;
    }

    public function getName() {
        return $this->name;
    }

    /**
     * Adds a phonenumber to the user.
     *
     * @param CmsPhonenumber $phone
     */
    public function addPhonenumber(CmsPhonenumber $phone) {
        $this->phonenumbers[] = $phone;
        $phone->setUser($this);
    }

    public function getPhonenumbers() {
        return $this->phonenumbers;
    }

    public function addArticle(CmsArticle $article) {
        $this->articles[] = $article;
        $article->setAuthor($this);
    }

    public function addGroup(CmsGroup $group) {
        $this->groups[] = $group;
        $group->addUser($this);
    }

    public function getGroups() {
        return $this->groups;
    }

    public function removePhonenumber($index) {
        if (isset($this->phonenumbers[$index])) {
            $ph = $this->phonenumbers[$index];
            unset($this->phonenumbers[$index]);
            $ph->user = null;
            return true;
        }
        return false;
    }

    public function getAddress() { return $this->address; }

    public function setAddress(CmsAddress $address) {
        if ($this->address !== $address) {
            $this->address = $address;
            $address->setUser($this);
        }
    }

    public function getEmail() { return $this->email; }

    public function setEmail(CmsEmail $email = null) {
        if ($this->email !== $email) {
            $this->email = $email;

            if ($email) {
                $email->setUser($this);
            }
        }
    }
}
