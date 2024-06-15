<?php
namespace Machwert\TccdExamples\PageTitle;

use TYPO3\CMS\Core\PageTitle\AbstractPageTitleProvider;

class MyRecordTitleProvider extends AbstractPageTitleProvider
{
    /**
     * @param string $title
     */
    public function setTitle(string $title)
    {
        $this->title = $title;
    }
}