<?php

declare(strict_types=1);

/*
 * This file is part of the TYPO3 CMS project.
 *
 * It is free software; you can redistribute it and/or modify it under
 * the terms of the GNU General Public License, either version 2
 * of the License, or any later version.
 *
 * For the full copyright and license information, please read the
 * LICENSE.txt file that was distributed with this source code.
 *
 * The TYPO3 project - inspiring people to share!
 */

namespace Machwert\TccdExamples\Hooks;

use TYPO3\CMS\Seo\Canonical\CanonicalGenerator;

/**
 * Class to add the canonical tag to the page
 *
 * @internal this class is not part of TYPO3's Core API.
 */
class OwnCanonicalGenerator extends CanonicalGenerator
{

    public function generateChild(): string
    {

        $redirectIfDifferent = false;
        $canonicalLink = parent::generate();
        $canonicalUri = '';
        $pattern = '/href="([^"]+)"/';
        $possibleStatusCodeList = '301, 302, 307, 308';
        $possibleStatusCodes = $this->trimexplode(',', $possibleStatusCodeList);
        $possibleStatusCodes = array_map('intval', $possibleStatusCodes);

        if (preg_match($pattern, $canonicalLink, $matches)) {
            $canonicalUri = $matches[1];
        }

        $canonicalRedirectPathList = $this->typoScriptFrontendController->config['config']['tx_tccd_examples.']['canonicalRedirectPaths'] ?? null;
        if($canonicalRedirectPathList !== null) {
            $canonicalRedirectPaths = $this->trimexplode(',', $canonicalRedirectPathList);
        }

        $logEnabled = (bool) ($this->typoScriptFrontendController->config['config']['tx_tccd_examples.']['log.']['enable'] ?? false);
        $logFile = $this->typoScriptFrontendController->config['config']['tx_tccd_examples.']['log.']['file'] ?? null;
        $redirectEnabled = (bool) ($this->typoScriptFrontendController->config['config']['tx_tccd_examples.']['redirect.']['enable'] ?? false);
        $redirectStatusCode = (int) ($this->typoScriptFrontendController->config['config']['tx_tccd_examples.']['redirect.']['statusCode'] ?? 301);

        if(!in_array($redirectStatusCode, $possibleStatusCodes)) {
            $redirectStatusCode = 301;
        }

        if(is_array($canonicalRedirectPaths)) {
            foreach ($canonicalRedirectPaths as $value) {
                if (strpos($canonicalUri, $value) !== false) {
                    $redirectIfDifferent = true;
                    break;
                }
            }
        } else {
            $redirectIfDifferent = true;
        }

        if($redirectIfDifferent) {
            $request = $GLOBALS['TYPO3_REQUEST'];
            $normalizedParams = $request->getAttribute('normalizedParams');
            $requestUri = $normalizedParams->getRequestUrl();

            if($requestUri !== '' && $canonicalUri !== '') {

                $requestUri = str_replace('&amp;', '&', $requestUri);
                $canonicalUri = str_replace('&amp;', '&', $canonicalUri);

                if ($requestUri !== $canonicalUri) {
                    if ($logEnabled) {
                        $logEntry = 'TIME: '.date('d.m.y H:i:s',time()).chr(10).'FROM: '.$requestUri.chr(10).'TO: '.$canonicalUri.chr(10).chr(10);
                        file_put_contents($logFile, $logEntry, FILE_APPEND);
                    }
                    if ($redirectEnabled) {
                        header('Location: '.$canonicalUri, true, $redirectStatusCode);
                        exit;
                    }
                }
            }
        }

        return $canonicalLink;
    }

    public function trimexplode($delimiter, $string) {
        $array = explode($delimiter, $string);
        $array = array_map('trim', $array);
        return $array;
    }
}
