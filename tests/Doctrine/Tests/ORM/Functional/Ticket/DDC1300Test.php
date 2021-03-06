<?php

declare(strict_types=1);

namespace Doctrine\Tests\ORM\Functional\Ticket;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Tests\OrmFunctionalTestCase;

use function count;

/**
 * @group DDC-1300
 */
class DDC1300Test extends OrmFunctionalTestCase
{
    protected function setUp(): void
    {
        parent::setUp();
        $this->_schemaTool->createSchema(
            [
                $this->_em->getClassMetadata(DDC1300Foo::class),
                $this->_em->getClassMetadata(DDC1300FooLocale::class),
            ]
        );
    }

    public function testIssue(): void
    {
        $foo                = new DDC1300Foo();
        $foo->_fooReference = 'foo';

        $this->_em->persist($foo);
        $this->_em->flush();

        $locale          = new DDC1300FooLocale();
        $locale->_foo    = $foo;
        $locale->_locale = 'en';
        $locale->_title  = 'blub';

        $this->_em->persist($locale);
        $this->_em->flush();

        $query  = $this->_em->createQuery('SELECT f, fl FROM Doctrine\Tests\ORM\Functional\Ticket\DDC1300Foo f JOIN f._fooLocaleRefFoo fl');
        $result =  $query->getResult();

        $this->assertEquals(1, count($result));
    }
}

/**
 * @Entity
 */
class DDC1300Foo
{
    /**
     * @var int fooID
     * @Column(name="fooID", type="integer", nullable=false)
     * @GeneratedValue(strategy="AUTO")
     * @Id
     */
    public $_fooID = null;

    /**
     * @var string fooReference
     * @Column(name="fooReference", type="string", nullable=true, length=45)
     */
    public $_fooReference = null;

    /**
     * @OneToMany(targetEntity="DDC1300FooLocale", mappedBy="_foo",
     * cascade={"persist"})
     */
    public $_fooLocaleRefFoo = null;

    /**
     * Constructor
     *
     * @param array|Zend_Config|null $options
     *
     * @return Bug_Model_Foo
     */
    public function __construct($options = null)
    {
        $this->_fooLocaleRefFoo = new ArrayCollection();
    }
}

/**
 * @Entity
 */
class DDC1300FooLocale
{
    /**
     * @ManyToOne(targetEntity="DDC1300Foo")
     * @JoinColumn(name="fooID", referencedColumnName="fooID")
     * @Id
     */
    public $_foo = null;

    /**
     * @var string locale
     * @Column(name="locale", type="string", nullable=false, length=5)
     * @Id
     */
    public $_locale = null;

    /**
     * @var string title
     * @Column(name="title", type="string", nullable=true, length=150)
     */
    public $_title = null;
}
