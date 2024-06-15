<?php

declare(strict_types=1);

namespace Machwert\TccdExamples\ViewHelpers;

use TYPO3\CMS\Fluid\Core\Rendering\RenderingContext;
use TYPO3Fluid\Fluid\Core\Rendering\RenderingContextInterface;
use TYPO3Fluid\Fluid\Core\ViewHelper\AbstractViewHelper;

final class CategoryCounterViewHelper extends AbstractViewHelper
{
    public static function renderStatic(array $arguments, \Closure $renderChildrenClosure, RenderingContextInterface $renderingContext): string
    {
        // /** @var RenderingContext $renderingContext */
        // $request = $renderingContext->getRequest();
        return 'ViewHelperTest';
    }
}