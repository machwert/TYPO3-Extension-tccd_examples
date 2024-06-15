<?php

declare(strict_types=1);

namespace Machwert\TccdExamples\Controller;

use DERHANSEN\SfEventMgt\Event\AfterRegistrationCancelledEvent;
use Machwert\TccdExamples\Event\DoingThisAndThatEvent;
use Machwert\TccdExamples\Event\ModifyPagetitleEvent;
use Machwert\TccdExamples\Event\OwnTccdExamplesEvent;
use Psr\Log\LoggerInterface;
use TYPO3\CMS\Core\Context\Context;
use TYPO3\CMS\Core\Core\Environment;
use TYPO3\CMS\Core\Messaging\FlashMessage;
use TYPO3\CMS\Core\MetaTag\MetaTagManagerRegistry;
use TYPO3\CMS\Core\Utility\GeneralUtility;

/**
 * This file is part of the "TCCD examples" Extension for TYPO3 CMS.
 *
 * For the full copyright and license information, please read the
 * LICENSE.txt file that was distributed with this source code.
 *
 * (c) 2024 Volker Golbig <typo3@machwert.de>, machwert
 */

/**
 * TccdController
 */
class TccdController extends \TYPO3\CMS\Extbase\Mvc\Controller\ActionController
{

    // Logging API [12]
    // PSR-3: Logger Interface standard [17]
    public function __construct(
        private readonly LoggerInterface $logger,
        private readonly Context $context
    ) {
    }

    /**
     * tccdRepository
     *
     * @var \Machwert\TccdExamples\Domain\Repository\TccdRepository
     */
    protected $tccdRepository = null;

    /**
     * @param \Machwert\TccdExamples\Domain\Repository\TccdRepository $tccdRepository
     */
    public function injectTccdRepository(\Machwert\TccdExamples\Domain\Repository\TccdRepository $tccdRepository)
    {
        $this->tccdRepository = $tccdRepository;
    }

    /**
     * action index
     *
     * @return \Psr\Http\Message\ResponseInterface
     */
    public function indexAction(): \Psr\Http\Message\ResponseInterface // PSR-7: Request/Response [15]
    {
        return $this->htmlResponse();
    }

    /**
     * action list
     *
     * @return \Psr\Http\Message\ResponseInterface
     */
    public function listAction(): \Psr\Http\Message\ResponseInterface
    {

        $searchTerm = '';
        $urlParameter = $this->getFilterParameter();

        if(isset($urlParameter['category'])) {
            $selectedCategory = (int) $urlParameter['category'];
            $this->view->assign('selectedCategory', $selectedCategory);
            $selectedCategoryArray[] = $selectedCategory;
            $tccds = $this->tccdRepository->findByCategory($selectedCategoryArray);
        } else {
            $tccds = $this->tccdRepository->findAll();
        }
        $this->view->assign('searchTerm', $searchTerm);
        $this->view->assign('tccds', $tccds);

        # Extend site configuration by extension [E]
        $site = $this->request->getAttribute('site');
        $siteConfigurationTestField = $site->getConfiguration()['siteConfigurationTestField'];
        $this->view->assign('siteConfigurationTestField', $siteConfigurationTestField);

        return $this->htmlResponse();
    }

    /**
     * action search
     *
     * @return \Psr\Http\Message\ResponseInterface
     */
    public function searchAction(): \Psr\Http\Message\ResponseInterface
    {
        $searchTerm = '';

        $filterParameter = $this->getFilterParameter();

        #Use application context [F]
        $applicationContext = Environment::getContext();
        if ($applicationContext->isDevelopment()) {
            debug($filterParameter);
        }

        if (isset($filterParameter['sword'])) {
            $searchTerm = $filterParameter['sword'];
        }
        foreach ($filterParameter as $key => $val) {
            $searchParameter[strtolower($key)] = $val;
        }
        unset($searchParameter['sword']);

        $this->view->assign('searchTerm', $searchTerm);
        $this->view->assign('searchParameter', $searchParameter);
        if (count($filterParameter) > 0) {
            $tccds = $this->tccdRepository->findByParameter($filterParameter);
        } else {
            $tccds = $this->tccdRepository->findAll();
        }
        $this->view->assign('tccds', $tccds);
        return $this->htmlResponse();
    }

    /**
     * action filter
     *
     * @return \Psr\Http\Message\ResponseInterface
     */
    public function filterAction(): \Psr\Http\Message\ResponseInterface
    {
        $selectedCategory = "";
        $selectedMainCategory = "";

        $mainCategoryId = $this->settings['maincategoryId'];

        $language = $this->request->getAttribute('language');
        $languageId = $language->getLanguageId();

        // categorie filter buttons
        $tccdCategories = $this->tccdRepository->findAllCategories($languageId);
        $this->view->assign('tccdsCategories', $tccdCategories);
        $urlParameter = $this->getFilterParameter();

        if (isset($urlParameter['category'])) {
            $selectedCategory = (int)$urlParameter['category'];
            if($tccdCategories[$selectedCategory]['parent'] == $mainCategoryId) {
                $selectedMainCategory = $selectedCategory;
            } else {
                $selectedMainCategory = $tccdCategories[$selectedCategory]['parent'];
            }
        }

        // search field
        $searchTerm = '';
        if(is_array($urlParameter) && count($urlParameter) > 0 && isset($urlParameter['sword'])) {
            $searchTerm = $this->sanitize($urlParameter['sword']);
        }

        if(!isset($urlParameter['showHidden'])) {
            $urlParameter['showHidden'] = 0;
        }
        if(!isset($urlParameter['showDeleted'])) {
            $urlParameter['showDeleted'] = 0;
        }
        if(!isset($urlParameter['ignoreStorage'])) {
            $urlParameter['ignoreStorage'] = 0;
        }

        /*
        $this->addCacheTags('showdeleted_'.$urlParameter['showDeleted']);
        $this->addCacheTags('ignorestorage_'.$urlParameter['ignoreStorage']);
        $this->addCacheTags('showhidden_'.$urlParameter['showHidden']);
        */

        $this->view->assign('showHidden', $urlParameter['showHidden']);
        $this->view->assign('showDeleted', $urlParameter['showDeleted']);
        $this->view->assign('ignoreStorage', $urlParameter['ignoreStorage']);
        $this->view->assign('searchTerm', $searchTerm);
        $this->view->assign('selectedMainCategory', $selectedMainCategory);
        $this->view->assign('selectedCategory', $selectedCategory);
        return $this->htmlResponse();
    }


    /**
     * action show
     *
     * @param \Machwert\TccdExamples\Domain\Model\Tccd $tccd
     * @return \Psr\Http\Message\ResponseInterface
     */
    public function showAction(?\Machwert\TccdExamples\Domain\Model\Tccd $tccd = null): \Psr\Http\Message\ResponseInterface
    {
        $subCategoryArray = [];

        //$this->addFlashMessage('show Action', '', \TYPO3\CMS\Core\Messaging\AbstractMessage::WARNING);

        if($tccd == null) {
            $uri = $this->uriBuilder
                ->reset()
                ->setTargetPageUid((int)$this->settings['listPid'])
                ->build();

            // Logging API [12]
            // PSR-3: Logger Interface standard [17]
            $this->logger->warning('Detail view called without parameter');
            return $this->redirectToUri($uri);

        }

        // The Caching Framework [18]
        $this->addCacheTags('RecordName_' . $tccd->getUid());

        # Extending Existing Functionality [16] - PSR-14 events (Goal 2) - start
        $modifyPagetitleEvent = new ModifyPagetitleEvent();
        $this->eventDispatcher->dispatch($modifyPagetitleEvent);
        $modifyPagetitleEvent->setTccdExamplesPagetitle($tccd->getTitle());
        # end

        foreach($tccd->getCategories() as $key => $val) {
            if($val->getParent()->getUid() != $this->settings['maincategoryId']) {
                $subCategoryArray[] = $val->getUid();
            }
        }

        $categorytccds = $this->tccdRepository->findByCategory($subCategoryArray);

        $this->view->assign('categorytccds', $categorytccds);
        // [6] - Accessing FlexForm Data
        $this->view->assign('settings', $this->settings);
        $this->view->assign('tccd', $tccd);

        return $this->htmlResponse();
    }

    /**
     * action new
     *
     * @return \Psr\Http\Message\ResponseInterface
     */
    public function newAction(): ?\Psr\Http\Message\ResponseInterface
    {
        if ($this->context->getPropertyFromAspect('frontend.user', 'id') === 0 && $this->context->getPropertyFromAspect('backend.user', 'id') === 0) {
            // Flash Messages [21]
            $this->addFlashMessage('Create new items is not possible. No user is logged in.', '', \TYPO3\CMS\Core\Messaging\AbstractMessage::WARNING);
            $this->view->assign('userIsLoggedIn', 0);
        } else {
            $this->view->assign('userIsLoggedIn', 1);
        }
        return $this->htmlResponse();
    }

    /**
     * action create
     *
     * @param \Machwert\TccdExamples\Domain\Model\Tccd $newTccd
     */
    public function createAction(\Machwert\TccdExamples\Domain\Model\Tccd $newTccd)
    {
        if ($this->context->getPropertyFromAspect('frontend.user', 'id') === 0 && $this->context->getPropertyFromAspect('backend.user', 'id') === 0) {
            $this->addFlashMessage('Create new items is not possible. No user is logged in.', '', \TYPO3\CMS\Core\Messaging\AbstractMessage::WARNING);
            $this->view->assign('userIsLoggedIn', 0);
        } else {
            $this->view->assign('userIsLoggedIn', 1);
            $this->addFlashMessage('The object was created.', '', FlashMessage::OK);
            $this->tccdRepository->add($newTccd);
        }

        // $this->redirect('list',null,null,null,$this->settings['listPid']); // flash message is not shown in list view
        $this->redirect('new');

    }

    /**
     * action edit
     *
     * @param \Machwert\TccdExamples\Domain\Model\Tccd $tccd
     * @TYPO3\CMS\Extbase\Annotation\IgnoreValidation("tccd")
     * @return \Psr\Http\Message\ResponseInterface
     */
    public function editAction(\Machwert\TccdExamples\Domain\Model\Tccd $tccd): \Psr\Http\Message\ResponseInterface
    {
        if ($this->context->getPropertyFromAspect('frontend.user', 'id') === 0 && $this->context->getPropertyFromAspect('backend.user', 'id') === 0) {
            $this->addFlashMessage('Edit items is not possible. No user is logged in.', '', \TYPO3\CMS\Core\Messaging\AbstractMessage::WARNING);
            $this->view->assign('userIsLoggedIn', 0);
        } else {
            $this->view->assign('userIsLoggedIn', 1);
            $this->view->assign('tccd', $tccd);
        }
        return $this->htmlResponse();
    }

    /**
     * action update
     *
     * @param \Machwert\TccdExamples\Domain\Model\Tccd $tccd
     */
    public function updateAction(\Machwert\TccdExamples\Domain\Model\Tccd $tccd)
    {
        if ($this->context->getPropertyFromAspect('frontend.user', 'id') === 0 && $this->context->getPropertyFromAspect('backend.user', 'id') === 0) {
            $this->addFlashMessage('Update items is not possible. No user is logged in.', '', \TYPO3\CMS\Core\Messaging\AbstractMessage::WARNING);
            $this->view->assign('userIsLoggedIn', 0);
        } else {
            $this->view->assign('userIsLoggedIn', 1);
            $this->addFlashMessage('The object was updated. Please be aware that this action is publicly accessible unless you implement an access check. See https://docs.typo3.org/p/friendsoftypo3/extension-builder/master/en-us/User/Index.html', '', \TYPO3\CMS\Core\Messaging\AbstractMessage::WARNING);
            $this->tccdRepository->update($tccd);
        }
        $this->redirect('list');
    }

    /**
     * action delete
     *
     * @param \Machwert\TccdExamples\Domain\Model\Tccd $tccd
     */
    public function deleteAction(\Machwert\TccdExamples\Domain\Model\Tccd $tccd)
    {
        if ($this->context->getPropertyFromAspect('frontend.user', 'id') === 0 && $this->context->getPropertyFromAspect('backend.user', 'id') === 0) {
            $this->addFlashMessage('Delete items is not possible. No user is logged in.', '', \TYPO3\CMS\Core\Messaging\AbstractMessage::WARNING);
            $this->view->assign('userIsLoggedIn', 0);
        } else {
            $this->view->assign('userIsLoggedIn', 1);
            $this->addFlashMessage('The object was deleted.', '', \TYPO3\CMS\Core\Messaging\AbstractMessage::WARNING);
            $this->tccdRepository->remove($tccd);
        }

        $this->redirect('list');
    }

    /**
     * action translation
     *
     * @return \Psr\Http\Message\ResponseInterface
     */
    public function translationAction(): \Psr\Http\Message\ResponseInterface
    {
        if ($this->context->getPropertyFromAspect('frontend.user', 'id') === 0 && $this->context->getPropertyFromAspect('backend.user', 'id') === 0) {
            $this->addFlashMessage('Translate items is not possible. No user is logged in.', '', \TYPO3\CMS\Core\Messaging\AbstractMessage::WARNING);
            $this->view->assign('userIsLoggedIn', 0);
        } else {
            $this->view->assign('userIsLoggedIn', 1);

            if ($this->settings['googleTranslatorApiKey'] == '') {
                die('Please set "module.tx_tccdexamples_tccdmodule.settings.googleTranslatorApiKey = YOUR-GOOGLE-TRANSLATOR-API-KEY" in TS Constants');
            }

            $websiteLanguages = $this->tccdRepository->findAllLanguages();
            $this->view->assign('websiteLanguages', $websiteLanguages);

            $getParameter = $this->request->getArguments();

            if (isset($getParameter['language'])) {
                $translateToLanguageId = (int)$getParameter['language'];
            } else {
                $translateToLanguageId = 0;
            }

            if ($translateToLanguageId > 0) {
                //$tccdsAll = $this->tccdRepository->findAllDontRespectStoragePage();
                $tccdsAll = $this->tccdRepository->findAll();
                $tccdsTranslated = $this->tccdRepository->findTranslated($translateToLanguageId);

                // remove already translated entries
                $removeObjects = [];
                foreach ($tccdsAll as $key => $object1) {
                    foreach ($tccdsTranslated as $key2 => $object2) {
                        if ($object1->getUid() === $object2->getL10nParent()) {
                            $removeObjects[] = $key;
                            break;
                        }
                    }
                }
                foreach ($removeObjects as $key => $val) {
                    unset($tccdsAll[$val]);
                }

                $translatedArray = $this->tccdRepository->translateDatasets($tccdsAll, $translateToLanguageId);
                $this->view->assign('translatedDatasets', $translatedArray['translatedDatasets']);
                $this->view->assign('translatedCount', $translatedArray['translatedCount']);
            }
        }
        return $this->htmlResponse();
    }

    public function sanitize($inputVar) {
        $inputVar = strip_tags($inputVar);
        $inputVar = htmlspecialchars($inputVar);
        return($inputVar);
    }
    public function getFilterParameter() {

        // PSR-7: Request/Response [15]
        $postParameterIntern = $this->request->getArguments();
        $postParameterExtern = $this->request->getParsedBody()['tx_tccdexamples_tccdpluginlist'] ?? null;
        $getParameterExtern = $this->request->getQueryParams()['tx_tccdexamples_tccdpluginlist'] ?? null;

        $urlParameter = $this->mergeAndRemoveDuplicates(
            $postParameterIntern,
            $postParameterExtern,
            $getParameterExtern
        );

        $filterParameter = [];

        if (is_array($urlParameter) && count($urlParameter) > 0) {
            if(isset($urlParameter['showhidden'])) {
                $filterParameter['showHidden'] = (int)$urlParameter['showhidden'];
            }
            if(isset($urlParameter['showdeleted'])) {
                $filterParameter['showDeleted'] = (int)$urlParameter['showdeleted'];
            }
            if(isset($urlParameter['ignorestorage'])) {
                $filterParameter['ignoreStorage'] = (int)$urlParameter['ignorestorage'];
            } else {
                $filterParameter['ignoreStorage'] = 0;
            }
            if(isset($urlParameter['category'])) {
                $filterParameter['category'] = (int)$urlParameter['category'];
            }
            if(isset($urlParameter['sword'])) {
                $filterParameter['sword'] = $this->sanitize($urlParameter['sword']);
            }
        }

        return $filterParameter;

    }

    public function mergeAndRemoveDuplicates(?array ...$arrays): array {
        // Initialisiere das Ergebnisarray
        $result = [];

        // Iteriere über jedes definierte und nicht leere Array
        foreach (array_filter($arrays, fn($arr) => isset($arr) && !empty($arr)) as $array) {
            // Füge die Werte des aktuellen Arrays mit dem +-Operator zusammen
            $result += $array;
        }

        // Filtere leere Werte
        $result = array_filter($result, function ($value) {
            return $value !== null && $value !== '' && (!is_array($value) || !empty($value));
        });

        // Iteriere erneut über das Ergebnisarray, um möglicherweise rekursive Zusammenführungen durchzuführen
        foreach ($result as $key => $value) {
            if (is_array($value) && isset($arrays[0][$key]) && is_array($arrays[0][$key])) {
                // Wenn es ein mehrdimensionales Array ist, wende die rekursive Zusammenführung an
                $result[$key] = mergeAndRemoveDuplicates($arrays[0][$key], $value);
            }
        }

        return $result;
    }

    // The Caching Framework [18]
    public function addCacheTags($cacheTag) {
        // do this only in frontend
        if (!empty($GLOBALS['TSFE']) && is_object($GLOBALS['TSFE'])) {
            // only set the tag once in one request, so cache statically if it has been done
            static $cacheTagsSet = FALSE;

            /** @var $typoScriptFrontendController \TYPO3\CMS\Frontend\Controller\TypoScriptFrontendController  */
            $typoScriptFrontendController = $GLOBALS['TSFE'];
            if (!$cacheTagsSet) {
                $typoScriptFrontendController->addCacheTags(array($cacheTag));
                $cacheTagsSet = TRUE;
            }
            $this->typoScriptFrontendController = $typoScriptFrontendController;
        }
    }

    public function testTccdEvent(): void
    {
        /** @var OwnTccdExamplesEvent $event */
        $event = $this->eventDispatcher->dispatch(
            new OwnTccdExamplesEvent('foo', 2),
        );
        $someChangedValue = $event->getMutableProperty();

        // ...
    }
}
