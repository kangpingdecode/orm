<?php

declare(strict_types=1);

namespace Doctrine\Tests\Models\Cache;

use Doctrine\Common\Collections\ArrayCollection;

/**
 * @Entity
 * @Table("cache_action")
 */
class Action
{
    /**
     * @var string
     * @Id
     * @Column(type="string")
     * @GeneratedValue(strategy="NONE")
     */
    public $name;

    /** @OneToMany(targetEntity="Token", cascade={"persist", "remove"}, mappedBy="action") */
    public $tokens;

    public function __construct($name)
    {
        $this->name   = $name;
        $this->tokens = new ArrayCollection();
    }

    public function addToken(Token $token): void
    {
        $this->tokens[] = $token;
        $token->action  = $this;
    }
}
