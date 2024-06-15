<?php

declare(strict_types=1);

namespace Machwert\TccdExamples\Tests\Unit\Domain\Model;

use PHPUnit\Framework\MockObject\MockObject;
use TYPO3\TestingFramework\Core\AccessibleObjectInterface;
use TYPO3\TestingFramework\Core\Unit\UnitTestCase;

/**
 * Test case
 *
 * @author Volker Golbig <typo3@machwert.de>
 */
class TccdTest extends UnitTestCase
{
    /**
     * @var \Machwert\TccdExamples\Domain\Model\Tccd|MockObject|AccessibleObjectInterface
     */
    protected $subject;

    protected function setUp(): void
    {
        parent::setUp();

        $this->subject = $this->getAccessibleMock(
            \Machwert\TccdExamples\Domain\Model\Tccd::class,
            ['dummy']
        );
    }

    protected function tearDown(): void
    {
        parent::tearDown();
    }

    /**
     * @test
     */
    public function getTitleReturnsInitialValueForString(): void
    {
        self::assertSame(
            '',
            $this->subject->getTitle()
        );
    }

    /**
     * @test
     */
    public function setTitleForStringSetsTitle(): void
    {
        $this->subject->setTitle('Conceived at T3CON10');

        self::assertEquals('Conceived at T3CON10', $this->subject->_get('title'));
    }

    /**
     * @test
     */
    public function getDescriptionReturnsInitialValueForString(): void
    {
        self::assertSame(
            '',
            $this->subject->getDescription()
        );
    }

    /**
     * @test
     */
    public function setDescriptionForStringSetsDescription(): void
    {
        $this->subject->setDescription('Conceived at T3CON10');

        self::assertEquals('Conceived at T3CON10', $this->subject->_get('description'));
    }

    /**
     * @test
     */
    public function getVersionReturnsInitialValueForInt(): void
    {
        self::assertSame(
            0,
            $this->subject->getVersion()
        );
    }

    /**
     * @test
     */
    public function setVersionForIntSetsVersion(): void
    {
        $this->subject->setVersion(12);

        self::assertEquals(12, $this->subject->_get('version'));
    }

    /**
     * @test
     */
    public function getSlugReturnsInitialValueForString(): void
    {
        self::assertSame(
            '',
            $this->subject->getSlug()
        );
    }

    /**
     * @test
     */
    public function setSlugForStringSetsSlug(): void
    {
        $this->subject->setSlug('Conceived at T3CON10');

        self::assertEquals('Conceived at T3CON10', $this->subject->_get('slug'));
    }

    /**
     * @test
     */
    public function geteditedReturnsInitialValueForBool(): void
    {
        self::assertFalse($this->subject->getedited());
    }

    /**
     * @test
     */
    public function seteditedForBoolSetsedited(): void
    {
        $this->subject->setedited(true);

        self::assertEquals(true, $this->subject->_get('edited'));
    }
}
