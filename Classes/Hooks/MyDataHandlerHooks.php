<?php
namespace Machwert\TccdExamples\Hooks;
use TYPO3\CMS\Core\DataHandling\DataHandler;

// source: https://stackoverflow.com/questions/74543820/updating-data-after-backend-action-typo3-11-5

// Extending Existing Functionality [16] - Hooks

class MyDataHandlerHooks {

    /**
     * @param string|int $id
     */
    public function processDatamap_postProcessFieldArray(
        string $status,         // Status of the current operation, 'new' or 'update'
        string $table,          // The table currently processing data for
               $id,             // The record uid currently processing data for,
                                // [integer] or [string] (like 'NEW...')
        array &$fieldArray,     // The field array of a record, cleaned to only
                                // 'to-be-changed' values. Needs to be &$fieldArray to be considered reference.
        DataHandler $dataHandler
    ): void
    {
        // $fieldArray may be stripped down to only the real fields which
        // needs to be updated, mainly for $status === 'update'. So if you
        // need to be sure to have correct data you may have to retrieve
        // the record to get the current value, if not provided as with new
        // value.

        if ($table === 'tx_tccdexamples_domain_model_tccd'
            && $status === 'update'
            && array_key_exists('title', $fieldArray)
        ) {
            $valueToReactTo = $fieldArray['title'];
            if ($valueToReactTo === 'Test Datahandler Hook') {
                $fieldArray['description'] = 'Test Datahandler Hook Description';
            }
        }
    }
}