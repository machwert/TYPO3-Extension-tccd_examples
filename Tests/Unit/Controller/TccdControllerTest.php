<?php

declare(strict_types=1);

namespace Machwert\TccdExamples\Tests\Unit\Controller;

use PHPUnit\Framework\MockObject\MockObject;
use TYPO3\TestingFramework\Core\AccessibleObjectInterface;
use TYPO3\TestingFramework\Core\Unit\UnitTestCase;
use TYPO3Fluid\Fluid\View\ViewInterface;

/**
 * Test case
 *
 * @author Volker Golbig <typo3@machwert.de>
 */
class TccdControllerTest extends UnitTestCase
{
    /**
     * @var \Machwert\TccdExamples\Controller\TccdController|MockObject|AccessibleObjectInterface
     */
    protected $subject;

    protected function setUp(): void
    {
        parent::setUp();
        $this->subject = $this->getMockBuilder($this->buildAccessibleProxy(\Machwert\TccdExamples\Controller\TccdController::class))
            ->onlyMethods(['redirect', 'forward', 'addFlashMessage'])
            ->disableOriginalConstructor()
            ->getMock();
    }

    protected function tearDown(): void
    {
        parent::tearDown();
    }

    /**
     * @test
     */
    public function listActionFetchesAllTccdsFromRepositoryAndAssignsThemToView(): void
    {
        $allTccds = $this->getMockBuilder(\TYPO3\CMS\Extbase\Persistence\ObjectStorage::class)
            ->disableOriginalConstructor()
            ->getMock();

        $tccdRepository = $this->getMockBuilder(\Machwert\TccdExamples\Domain\Repository\TccdRepository::class)
            ->onlyMethods(['findAll'])
            ->disableOriginalConstructor()
            ->getMock();
        $tccdRepository->expects(self::once())->method('findAll')->will(self::returnValue($allTccds));
        $this->subject->_set('tccdRepository', $tccdRepository);

        $view = $this->getMockBuilder(ViewInterface::class)->getMock();
        $view->expects(self::once())->method('assign')->with('tccds', $allTccds);
        $this->subject->_set('view', $view);

        $this->subject->listAction();
    }

    /**
     * @test
     */
    public function showActionAssignsTheGivenTccdToView(): void
    {
        $tccd = new \Machwert\TccdExamples\Domain\Model\Tccd();

        $view = $this->getMockBuilder(ViewInterface::class)->getMock();
        $this->subject->_set('view', $view);
        $view->expects(self::once())->method('assign')->with('tccd', $tccd);

        $this->subject->showAction($tccd);
    }

    /**
     * @test
     */
    public function createActionAddsTheGivenTccdToTccdRepository(): void
    {
        $tccd = new \Machwert\TccdExamples\Domain\Model\Tccd();

        $tccdRepository = $this->getMockBuilder(\Machwert\TccdExamples\Domain\Repository\TccdRepository::class)
            ->onlyMethods(['add'])
            ->disableOriginalConstructor()
            ->getMock();

        $tccdRepository->expects(self::once())->method('add')->with($tccd);
        $this->subject->_set('tccdRepository', $tccdRepository);

        $this->subject->createAction($tccd);
    }

    /**
     * @test
     */
    public function editActionAssignsTheGivenTccdToView(): void
    {
        $tccd = new \Machwert\TccdExamples\Domain\Model\Tccd();

        $view = $this->getMockBuilder(ViewInterface::class)->getMock();
        $this->subject->_set('view', $view);
        $view->expects(self::once())->method('assign')->with('tccd', $tccd);

        $this->subject->editAction($tccd);
    }

    /**
     * @test
     */
    public function updateActionUpdatesTheGivenTccdInTccdRepository(): void
    {
        $tccd = new \Machwert\TccdExamples\Domain\Model\Tccd();

        $tccdRepository = $this->getMockBuilder(\Machwert\TccdExamples\Domain\Repository\TccdRepository::class)
            ->onlyMethods(['update'])
            ->disableOriginalConstructor()
            ->getMock();

        $tccdRepository->expects(self::once())->method('update')->with($tccd);
        $this->subject->_set('tccdRepository', $tccdRepository);

        $this->subject->updateAction($tccd);
    }

    /**
     * @test
     */
    public function deleteActionRemovesTheGivenTccdFromTccdRepository(): void
    {
        $tccd = new \Machwert\TccdExamples\Domain\Model\Tccd();

        $tccdRepository = $this->getMockBuilder(\Machwert\TccdExamples\Domain\Repository\TccdRepository::class)
            ->onlyMethods(['remove'])
            ->disableOriginalConstructor()
            ->getMock();

        $tccdRepository->expects(self::once())->method('remove')->with($tccd);
        $this->subject->_set('tccdRepository', $tccdRepository);

        $this->subject->deleteAction($tccd);
    }
}
