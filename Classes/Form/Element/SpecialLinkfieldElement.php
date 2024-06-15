<?php
declare(strict_types = 1);
namespace Machwert\TccdExamples\Form\Element;

use TYPO3\CMS\Backend\Form\Element\AbstractFormElement;
use TYPO3\CMS\Backend\Form\Element\InputLinkElement;

# Extending Existing Functionality [16] - 3-2 userFuncs in the TCA

class SpecialLinkfieldElement extends AbstractFormElement
{

    public function render():array
    {
        $htmlAdd = '';
        $parameterArray = $this->data['parameterArray'];
        $itemFormElName = $parameterArray['itemFormElName'];
        $itemValue = $parameterArray['itemFormElValue'];

        if($itemValue != '') {
            $itemValue .= ',';
        }
        $itemValueArray = explode(",", $itemValue);

        $newData = $this->data;

        foreach($itemValueArray as $key => $val) {
            $newData['parameterArray']['itemFormElValue'] = $val;
            if ($key > 0) {
                $newId = 10500 + $key;
                $newData['parameterArray']['itemFormElName'] = 'data[tx_tccdexamples_domain_model_tccd]['.$newId.'][links]';
                $newData['parameterArray']['itemFormElID'] = 'data_tx_tccdexamples_domain_model_tccd_'.$newId.'_links';
            }

            $inputLinkElement2 = new InputLinkElement($this->nodeFactory, $newData);
            $resultArray2 = $inputLinkElement2->render();

            if ($key == 0) {
                $resultArray = $resultArray2;
            } else {
                preg_match_all('/id="([^"]+)"/', $resultArray2['html'], $matches);
                $idArray = $matches[1];
                foreach ($idArray as $id) {
                    if (strpos($id, 't3js-formengine-') === 0) {
                        $resultArray['requireJsModules'][0]->instance('#'.$id);
                    }
                    if (strpos($id, 'formengine-input-') === 0) {
                        $resultArray['requireJsModules'][1]->instance($id);
                    }
                }
            }

            $htmlAdd .= $resultArray2['html'];
        }

        $htmlAdd .= "
        <script>
     
        var button = document.querySelector('button[name=\"_savedok\"]');

        button.addEventListener('click', function(event) {
            var inputElements = document.querySelectorAll('input[name^=\"data[tx_tccdexamples_domain_model_tccd][\"][name$=\"][links]\"], input[data-formengine-input-name^=\"data[tx_tccdexamples_domain_model_tccd][\"][data-formengine-input-name$=\"][links]\"]');
            //var inputElements = document.querySelectorAll('input[name^=\"data[tx_tccdexamples_domain_model_tccd][\"][name$=\"][links]\"]');

            var values = [];
            
            inputElements.forEach(function(input) {
                if (input.value && !values.includes(input.value)) {
                    values.push(input.value);
                }
                
                input.value = '';
                
                if (input.name != '".$itemFormElName."') {
                    input.remove();
                }
                
                var previousInput = findPreviousInput(input);
                if (previousInput) {
                    previousInput.value = '';
                }
                
            });
            
            var commaSeparatedString = values.join(',');
            if (commaSeparatedString.endsWith(',')) {
                commaSeparatedString = commaSeparatedString.slice(0, -1);
            }

            var targetInput = document.querySelector('input[name=\"".$itemFormElName."\"]');
            if (targetInput) {
                targetInput.value = commaSeparatedString;
            }

            // event.preventDefault();
        });
        
        function findPreviousInput(element) {
            var previousSibling = element.previousElementSibling;
            while (previousSibling) {
                if (previousSibling.classList.contains('t3js-form-field-inputlink-explanation') && previousSibling.tagName.toLowerCase() === 'input') {
                    return previousSibling;
                }
                previousSibling = previousSibling.previousElementSibling;
            }
            return null;
        }
    
        </script>
        ";

        $resultArray['html'] = $htmlAdd;
        return $resultArray;
    }
}